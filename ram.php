<?php

//$re = '/^\s*(\d+)\s+(\w+)\s+(.+)?(\d.\d\d\%)\s+(\d.\d\d\%)\s+(\d.\d\d\%)\s+(\d.\d\d\%)\s*$/m';
//exec("smem -p -H", $str);

$re = '/^\s*(\d+)\s+(\w+)\s+([\d\.]+)\s*(.+?)$/m';
exec("ps -o pid=,user=,rss=,command= ax", $str);

//$re = '/^\s*(\d+)\s+(.+?)\s+(\d+) kB\s*$/m';
//exec('find /proc -maxdepth 2 -path "/proc/[0-9]*/status" -readable -exec awk -v FS=":" \'{process[$1]=$2;sub(/^[ \t]+/,"",process[$1]);} END {if(process["VmSwap"] && process["VmSwap"] != "0 kB") printf "%10s %-30s %20s\n",process["Pid"],process["Name"],process["VmSwap"]}\' \'{}\' \;', $str);

$str = join("\n", $str);

function getCommand($cmd)
{
    $cmd = trim($cmd);
    $cmd = preg_split("/\s+/", $cmd)[0];
    $return = $cmd;
    if ($cmd[0] === '/') {
        $return = basename($cmd);
    }
    if ($cmd[0] === '[') {
        $return = $cmd[0];
    }
    $return = preg_replace("/^java$/", 'IDEA', $return);
    return $return;
}

$colors = [
    "#F44336",
    "#E91E63",
    "#9C27B0",
    "#673AB7",
    "#3F51B5",
    "#2196F3",
    "#03A9F4",
    "#00BCD4",
    "#009688",
    "#4CAF50",
    "#8BC34A",
    "#CDDC39",
    "#FFEB3B",
    "#FFC107",
    "#FF9800",
    "#FF5722",
    "#795548",
    "#9E9E9E",
    "#607D8B",
];
shuffle($colors);

$knownCommands = [
    "chrome" => "pattern.draw('circle', '#4a8cf5')",
    "IDEA" => "pattern.draw('square', '#fe315d')",
    "node" => "pattern.draw('triangle', '#026e00')",
];

$commandsRam = [];
preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
foreach ($matches as $match) {
    $data = [
        "PID" => $match[1],
        "RSS" => floatval($match[3]) / 1024 / 1024,
        "Command" => getCommand($match[4]),
    ];
    if (array_key_exists($data["Command"], $commandsRam)) {
        $commandsRam[$data["Command"]]['ram'] += $data["RSS"];
    } else {
        $commandsRam[$data["Command"]]['ram'] = $data["RSS"];
        if (array_key_exists($data["Command"], $knownCommands)) {
            $commandsRam[$data["Command"]]['color'] = $knownCommands[$data["Command"]];
        } else {
            if (count($colors) > 0) {
                $commandsRam[$data["Command"]]['color'] = array_pop($colors);
            } else {
                $commandsRam[$data["Command"]]['color'] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));;
            }
        }

    }

    uasort($commandsRam, function ($a, $b) {
        if ($a['ram'] == $b['ram']) {
            return 0;
        }
        return ($a['ram'] < $b['ram']) ? -1 : 1;
    });
    foreach ($commandsRam as $cmd => $data) {
        if ($data['ram'] < 0.2) {
            unset($commandsRam[$cmd]);
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>RAM</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/patternomaly@1.3.2/dist/patternomaly.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>
</head>
<body style="margin: 0; padding: 0; height: 100vh;">
<canvas id="myChart"></canvas>
<script>
  function hexToRgb(hex) {
    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, function (m, r, g, b) {
      return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
      r: parseInt(result[1], 16),
      g: parseInt(result[2], 16),
      b: parseInt(result[3], 16)
    } : null;
  }

  var ctx = document.getElementById('myChart');
  var myChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: [
        '<?= join("','", array_keys($commandsRam)); ?>'
      ],
      datasets: [{
        label: 'Swap, MB',
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
      legend: {
        reverse: true
      },
      plugins: {
        labels: {
          render: 'percentage',
          fontColor: '#000',
          fontWeight: 'bold',
          precision: 2
        }
      }
    }
  });
</script>
</body>
</html>
