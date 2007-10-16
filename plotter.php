<?php
class Plotter
{
    private $plot_dir = "";
    private $plot_data = "";
    private $gnuplot_data;

    function __construct($plot_dir, $plot_data)
    {
        $this->plot_dir = $plot_dir;
        $this->plot_data = $plot_data;
        $this->clean_up_dir();
        $this->gnuplot_data = fopen($this->plot_data, 'w');
    }

    function get_plot_data()
    {
        return $plot_data;
    }

    function data($plot_string)
    {
        fwrite($this->gnuplot_data, "$plot_string\n");
    }

    /** Increase or decrease scale of x axis.
     * $factor is a floating point scale factor.
     */
    function change_scale($factor = 2)
    {
    }

    /** Starting X coordinate of graph. */
    function slide_window($factor)
    {
    }

    function plot($png_name, $title)
    {
        fclose($this->gnuplot_data);

        $gnuplot_cmds_name = tempnam('/tmp', '');
        $gnuplot_cmds = fopen($gnuplot_cmds_name, 'w');
        $png_file = "$png_name.png";

        fwrite($gnuplot_cmds,
               "set terminal png transparent xffffff size 500,375\n" .
               "set output \"plots/$png_file\"\n" .
               "set xdata time\n" .
               "set timefmt \"%Y/%m/%d\"\n" .
               "set yrange [0:100]\n" .
               "set format x \"%d/%m/%y\"\n" .
               "set xtics 1209600\n" .
               "set timefmt \"%Y-%m-%d %H:%M:%S\"\n" .
               "plot \"$this->plot_data\" using 1:3 title \"$title\" " .
               "with lines\n");

        return system("gnuplot $gnuplot_cmds_name", $retvar);
    }

    private function clean_up_dir()
    {
        // Clean plots that haven't been accessed in 5 minutes or more.
        $plot_diri = dir($this->plot_dir);
        while (false !== ($entry = $plot_diri->read())) {
            if ($entry != '.' && $entry != '..' &&
                (time() - fileatime("/var/www/plots/$entry") >= 5*60))
                unlink("/var/www/plots/$entry");
        }
        $plot_diri->close();
    }
}
?>