<?php

exec('cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq', $frequency);
echo $frequency[0]/1000000;
