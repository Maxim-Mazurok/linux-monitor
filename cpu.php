<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CPU</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/0.5.7/chartjs-plugin-annotation.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
</head>
<body style="margin: 0; padding: 0; height: 100vh;">
<canvas id="myChart"></canvas>
<script>
  var ctx = document.getElementById('myChart');

  function addData(chart, label, data) {
    chart.data.labels.push(label);
    chart.data.datasets.forEach((dataset) => {
      dataset.data.push(data);
    });
    chart.update();
  }

  var myChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: [
        '<?= join("','", array_keys($commandsRam)); ?>'
      ],
      datasets: [{
        label: 'CPU Frequency, GHz',
        data: [
            <?= join(",", array_map(function ($item) {
            return $item['ram'];
        }, $commandsRam)); ?>
        ],
        backgroundColor: [
            <?= join(",", array_map(function ($item) {
            return $item['color'][0] === "#" ? "'{$item['color']}'" : $item['color'];
        }, $commandsRam)); ?>
        ],
        borderWidth: 1
      }]
    },
    options: {
      maintainAspectRatio: false,
      scales: {
        yAxes: [{
          display: true,
          ticks: {
            min: <?php require("./getCurrentCPUMinFrequency.php"); ?>,
            max: <?php require("./getCurrentCPUMaxFrequency.php"); ?>
          }
        }]
      },
      annotation: {
        annotations: [
          {
            type: 'line',
            mode: 'horizontal',
            scaleID: 'y-axis-0',
            value: 2.4,
            borderColor: 'blue',
            borderWidth: 1,
            label: {
              enabled: true,
              content: 'base clock speed'
            }
          }, {
            type: 'line',
            mode: 'horizontal',
            scaleID: 'y-axis-0',
            value: 2.7,
            borderColor: 'green',
            borderWidth: 1,
            label: {
              enabled: true,
              content: 'base clock speed'
            }
          }, {
            type: 'line',
            mode: 'horizontal',
            scaleID: 'y-axis-0',
            value: 3,
            borderColor: 'red',
            borderWidth: 1,
          },
        ]
      },
    }
  });

  setInterval(() => {
    $.ajax({
      url: "/getCurrentCPUCurrentFrequency.php",
      success: function (result) {
        addData(myChart, (new Date()).toLocaleString(), result);
      },
      error: function (error) {
        alert(JSON.stringify({ error }));
      }
    });
  }, 500);
</script>
</body>
</html>
