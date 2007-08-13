set terminal png transparent xffffff size 400,300
set output "/var/www/hs.png"
set xdata time
set timefmt "%Y/%m/%d"
set yrange [0:100]
set format x "%m/%y"
set timefmt "%Y-%m-%d %H:%M:%S"
plot "/tmp/plotdata" using 1:3 title "Win/Loss Ratio" with lines
