<script>
    import { Line } from 'vue-chartjs'
    export default {
        extends: Line,
        props: ['companies', 'current', 'last', 'rate'],
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
                            },
                            footer: function(tooltipItems, data) {
                                let diff = parseInt(tooltipItems[0].value - tooltipItems[1].value);
                                let rate = (tooltipItems[1].value == 0) ? 0 : (tooltipItems[0].value / tooltipItems[1].value - 1) * 100;
                                return  (diff > 0 ? "Aumentar: ": "Disminuir: ") + diff.toLocaleString() + " (" + Number.parseInt(rate) + "%" + ')';
                            }
                        },
                        afterBody: "string"
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
                }
            };
        },
        watch: {
            rate: function(){
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
                                label: this.$t('page.this_year'),
                                borderColor: '#2ec7c9',
                                data: this.current
                            },
                            {
                                label: this.$t('page.last_year'),
                                borderColor: '#d87a80',
                                data: this.last
                            }
                        ]
                    },
                    this.chartOption
                );
            }
        }
    }
</script>
