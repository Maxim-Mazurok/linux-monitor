<?php

exec('cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_max_freq', $cpuMaxFrequency);
echo $cpuMaxFrequency[0]/1000000;
