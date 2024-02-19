<?php

namespace App\Http\Controllers;

use App\Models\BackupLog;
use App\Models\Company;
use App\Models\Setting;
use App\Models\User;
use App\Traits\UploadAble;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SuperAdminController extends Controller
{
    use UploadAble;

    public function getDailyIncome(Request $request) {
        $date = $request->get('date');
        $date = substr($date, 0, 10);

        if (Auth::user()->role == 'super_admin') {
            $daily = [];
            foreach (Company::all() as $item) {
                $company_incoming = $item->transactions()->where('type', 'incoming')->whereDate('timestamp', $date)->get()->sum('amount');
                $daily[] = ['name' => $item->name, 'incoming' => $company_incoming];
            }
            $sorted = collect($daily)->sortByDesc('incoming');
            foreach($sorted->values()->all() as $row) {
                $data['name'][] = $row['name'];
                $data['incoming'][] = $row['incoming'];
            }
        }
        return $this->sendResponse($data);
    }

    public function getMonthlyRevenue(Request $request) {
        $date = $request->post('date');

        $year = substr($date, 0, 4);
        $month = substr($date, 5, 2);;
        $endDay = Carbon::now()->month($month)->daysInMonth;

        $startDate = $year. "-". $month. "-01";
        $endDate = $year. "-". $month. "-". $endDay;

        $data = [];
        if (Auth::user()->role == 'super_admin') {
            $monthly = [];
            foreach (Company::all() as $item) {
                $expense = $item->transactions()->where('type', 'expense')->whereBetween('timestamp', [$startDate, $endDate])->get()->sum('amount');
                $incoming = $item->transactions()->where('type', 'incoming')->whereBetween('timestamp', [$startDate, $endDate])->get()->sum('amount');
                $monthly[] = ['name' => $item->name, 'revenue' => $incoming - $expense];
            }
            $sorted = collect($monthly)->sortByDesc('revenue');
            foreach($sorted->values()->all() as $row) {
                $data['name'][] = $row['name'];
                $data['revenue'][] = $row['revenue'];
            }
        }
        return $this->sendResponse($data);
    }

    public function getCompareRevenue(Request $request) {
        $startDateLastYear = Carbon::parse($request->get('start_date_last'));
        $endDateLastYear = Carbon::parse($request->get('end_date_last'));
        $startDateCurrentYear = Carbon::parse($request->get('start_date_current'));
        $endDateCurrentYear = Carbon::parse($request->get('end_date_current'));

        $data = [];
        if (Auth::user()->role == 'super_admin') {
            $compare = [];
            foreach (Company::all() as $item) {
                $incoming = $item->transactions()->where('type', 'incoming')->whereBetween('timestamp', [$startDateCurrentYear, $endDateCurrentYear])->sum('amount');
                $incoming_last = $item->transactions()->where('type', 'incoming')->whereBetween('timestamp', [$startDateLastYear, $endDateLastYear])->sum('amount');

                $compare[] = [
                    'name' => $item->name,
                    'incoming' => $incoming,
                    'incoming_last' => $incoming_last,
                    'rate' => $incoming - $incoming_last
                ];
            }
            $sorted = collect($compare)->sortByDesc('rate');
            foreach($sorted->values()->all() as $row) {
                $data['name'][] = $row['name'];
                $data['incoming'][] = $row['incoming'];
                $data['incoming_last'][] = $row['incoming_last'];
                $data['rate'][] = $row['rate'];
            }
        }

        return $this->sendResponse($data);
    }

    public function uploadDatabaseBackup(Request $request) {
        ini_set('max_execution_time', 0);
        $request->validate([
            'backup_file' => 'required|file'
        ]);
        $fileName = 'backup_'. date('YmdHis');
        $path = $this->uploadFile($request->file('backup_file'), 'backups', 'public', $fileName);
        $filePath = storage_path("app/public/" . $path);
        $filePath = str_replace("\\", "/", $filePath);
        try {
            $this->restoreDatabase($filePath);
            $log = null;
            if (Schema::hasTable('backup_logs')) {
                $log = BackupLog::create([
                    'user_id' => Auth::id(),
                    'path' => $path,
                    'status' => 'completed',
                ]);
            }
            if (!config('app.test_database')) {
                User::changeRole();
            }
            return $this->sendResponse($log, 'Database restored successfully.');
        } catch (ProcessFailedException $e) {
            throw $e;
            return $this->sendErrors($e->getMessage(), 'Failed to restore database.');
        }
    }

    protected function restoreDatabase($filePath)
    {
        ini_set('max_execution_time', 0);
        $dbConnection = config('database.default');
        $database = config('app.test_database') ? config('app.test_database') : config('database.connections.'.$dbConnection.'.database');

        $username = config('database.connections.'.$dbConnection.'.username');
        $password = config('database.connections.'.$dbConnection.'.password');
        $host = config('database.connections.'.$dbConnection.'.host');
        $mysqlPath = config('app.mysql_path');
        $command = "$mysqlPath -u $username -p$password -h $host $database < $filePath";
        Log::info('SQL Backup Command: '. $command);

        $process = Process::fromShellCommandline($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
