mkdir /sys/kernel/config/iio/triggers/hrtimer/trigger1
mkdir /sys/kernel/config/iio/triggers/hrtimer/trigger2
mkdir /sys/kernel/config/iio/triggers/hrtimer/trigger3
echo trigger1 > /sys/bus/iio/devices/iio\:device1/trigger/current_trigger
echo trigger2 > /sys/bus/iio/devices/iio\:device2/trigger/current_trigger
echo trigger3 > /sys/bus/iio/devices/iio\:device3/trigger/current_trigger
cat /sys/bus/iio/devices/iio\:device1/trigger/current_trigger
cat /sys/bus/iio/devices/iio\:device2/trigger/current_trigger
cat /sys/bus/iio/devices/iio\:device3/trigger/current_trigger
