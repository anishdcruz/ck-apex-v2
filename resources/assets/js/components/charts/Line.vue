<template>
    <div>
        <canvas ref="line"></canvas>
    </div>
</template>
<script type="text/javascript">
    import Chart from 'chart.js'

    export default {
        props: ['labels', 'datasets'],
        data() {
            return {
                chart: {
                    datasets: this.datasets,
                    labels: this.labels
                },
            }
        },
        mounted() {
            const myChart = new Chart(this.$refs.line, {
                type: 'line',
                data: this.chart,
                options: {
                    animation: {
                        duration: 0, // general animation time
                    },
                    hover: {
                        animationDuration: 0, // duration of animations when hovering an item
                    },
                    responsiveAnimationDuration: 0, // animation duration after a resize
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            })
        }
    }
</script>
