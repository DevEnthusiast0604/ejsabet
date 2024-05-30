<script>
    import { Line } from 'vue-chartjs'
    export default {
        extends: Line,
        props: ['keys', 'companies', 'incoming'],
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
                let dataSets = this.incoming.map((value, index) => {
                    let randomColor = Math.floor(Math.random()*16777215).toString(16);
                    return {
                        label: this.companies[index],
                        borderColor: `#${randomColor}`,
                        data: value,
                    }
                });
                this.renderChart(
                    {
                        labels: this.keys,
                        datasets: dataSets
                    },
                    this.chartOption
                );
            },
        }
    }
</script>
