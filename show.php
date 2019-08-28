<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8"><link rel="icon" href="https://jscdn.com.cn/highcharts/images/favicon.ico">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            #allmap{
                width: 500px;
                height: 500px;
                float: left;
            }
             #panorama{
                width: 500px;
                height: 500px;
                float: right;
            }
        </style>
        <script src="https://code.highcharts.com.cn/highcharts/highcharts.js"></script>
        <script src="https://code.highcharts.com.cn/highcharts/highcharts-more.js"></script>
        <script src="https://code.highcharts.com.cn/highcharts/modules/exporting.js"></script>
        <script src="https://img.hcharts.cn/highcharts-plugins/highcharts-zh_CN.js"></script>
        <script type="text/javascript" src="//api.map.baidu.com/api?v=2.0&ak=y01uOWNlWjsUG980LN2O849eIotOuzGA"></script>
    </head>
    <body>
    <?php echo $data['redis']; ?>
        <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        <div id="allmap" ></div>
        <div id="panorama" ></div>

        <script>
           var chart = Highcharts.chart('container', {
    chart: {
        type: 'columnrange', // columnrange 依赖 highcharts-more.js
        inverted: true
    },
    title: {
        text: '温度变化范围'
    },
    subtitle: {
        text: "<?php echo $data['where'] ?>"
    },
    xAxis: {
        categories: <?php echo json_encode($data['days']); ?>
    },
    yAxis: {
        title: {
            text: '温度 ( °C )'
        }
    },
    tooltip: {
        valueSuffix: '°C'
    },
    plotOptions: {
        columnrange: {
            dataLabels: {
                enabled: true,
                formatter: function () {
                    return this.y + '°C';
                }
            }
        }
    },
    legend: {
        enabled: false
    },
    series: [{
        name: '温度',
        data: <?php echo json_encode($data['temp']); ?>
    }]
});
        </script>
    </body>
</html>
<script type="text/javascript">
    // 百度地图API功能
    var map = new BMap.Map("allmap");    // 创建Map实例
    map.centerAndZoom("<?php echo $data['where']; ?>",11);      // 初始化地图,用城市名设置地图中心点
</script>

<script type="text/javascript">
    var panorama = new BMap.Panorama('panorama');
    panorama.setPosition(new BMap.Point(<?php echo $point['lng']; ?>, <?php echo $point['lat']; ?>));
    
   
</script>