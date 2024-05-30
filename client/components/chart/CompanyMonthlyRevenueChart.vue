<script>
import { component } from 'v-viewer';
import { Bar } from 'vue-chartjs'
    export default {
        extends: Bar,
        props: ['companies', 'revenue'],
        data(){
            return {
                chartOption: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                      display: false,
                    },
                    tooltips: {
                        mode: 'index',
                        callbacks: {
                            label: function(tooltipItems, data) {
                                let value = parseInt(data.datasets[tooltipItems.datasetIndex].data[tooltipItems.index]).toLocaleString();
                                return data.datasets[tooltipItems.datasetIndex].label + ": " + value;
                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: false,
                                callback: function(value, index, values) {
                                    return value.toLocaleString();
                                }
                            }
                        }]
                    }
                },
            };
        },
        watch: {
          revenue: function(){
                this.showChart()
            }
        },
        mounted () {
            this.showChart();
        },
        methods: {
            showChart(){
                this.renderChart(
                    {
                        labels: this.companies,
                        datasets: [
                            {
                                label: this.$t('page.revenue'),
                                backgroundColor: '#7ad880',
                                data: this.revenue
                            }
                        ]
                    },
                    this.chartOption
                );
            },
        }
    }
</script>
