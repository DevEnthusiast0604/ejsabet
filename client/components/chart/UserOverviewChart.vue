<script>
    import { Bar } from 'vue-chartjs'
    export default {
        extends: Bar,
        props: ['users', 'expense', 'incoming'],
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
                        labels: this.users,
                        datasets: [
                            {
                                label: this.$t('page.expense'),
                                backgroundColor: '#2ec7c9',
                                data: this.expense
                            },
                            {
                                label: this.$t('page.incoming'),
                                backgroundColor: '#d87a80',
                                data: this.incoming
                            }
                        ]
                    },
                    this.chartOption
                );
            },
        }
    }
</script>
