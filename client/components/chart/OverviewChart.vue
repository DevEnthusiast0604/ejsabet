<script>
    import { Line } from 'vue-chartjs'
    export default {
        extends: Line,
        props: ['keys', 'total_expenses', 'total_incomings'],
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
            keys: function(){
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
                        labels: this.keys,
                        datasets: [
                            {
                                label: this.$t('page.expense'),
                                borderColor: '#2ec7c9',
                                data: this.total_expenses
                            },
                            {
                                label: this.$t('page.incoming'),
                                borderColor: '#d87a80',
                                data: this.total_incomings
                            }
                        ]
                    },
                    this.chartOption
                );
            },
        }
    }
</script>
