<script>
    import { Pie } from 'vue-chartjs'
    export default {
        extends: Pie,
        props: ['companies', 'balance'],
        data(){
            return {
                chartOption: {
                    responsive: true,
                    maintainAspectRatio: false,
                    tooltips: {
                        mode: 'index',
                        callbacks: {
                            label: function(tooltipItems, data) {
                                let value = parseInt(data.datasets[tooltipItems.datasetIndex].data[tooltipItems.index]).toLocaleString();
                                return data.labels[tooltipItems.index] + ": " + value;
                            }
                        }
                    },
                    legend: {
                        position: 'right',
                    }
                },
            };
        },
        watch: {
            keys: function(){
                this.showChart()
            }
        },
        mounted () {
            this.showChart();
        },
        methods: {
            showChart(){
                let colors = this.companies.map(i => '#' + Math.floor(Math.random()*16777215).toString(16));
                this.renderChart(
                    {
                        labels: this.companies,
                        datasets: [
                            {
                                // label: this.$t('page.balance'),
                                backgroundColor: colors,
                                data: this.balance
                            },
                        ]
                    },
                    this.chartOption
                );
            },
        }
    }
</script>
