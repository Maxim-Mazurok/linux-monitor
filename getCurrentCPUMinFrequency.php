<?php

exec('cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_min_freq', $cpuMinFrequency);
echo $cpuMinFrequency[0]/1000000;
