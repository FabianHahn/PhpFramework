<?php
require_once "LineChartStyle.php";

/**
 * Class to draw simple line charts
 */
class LineChart
{
	/**
	 * Width of the chart
	 * @var int
	 */
	protected $width;
	
	/**
	 * Height of the chart
	 * @var int
	 */
	protected $height;
	
	/**
	 * Distance between x axis labels
	 * @var int
	 */
	protected $x_delta;
	
	/**
	 * Distance between y axis labels
	 * @var int
	 */
	protected $y_delta;
	
	/**
	 * The title of the chart
	 * @var string
	 */
	protected $title;
	
	/**
	 * The description of the x axis
	 * @var string
	 */
	protected $x_descr;
	
	/**
	 * The description of the y axis
	 * @var string
	 */
	protected $y_descr;
	
	/**
	 * The style this chart uses
	 * @var LineChartStyle
	 */
	protected $style;

	/**
	 * The generated image
	 * @var resource
	 */
	protected $img;
	
	/**
	 * The chart data
	 * @var array[array[int]]
	 */
	protected $data;
	
	/**
	 * The maximum string length of x axis data
	 * @var int
	 */
	protected $maxlen_x;
	
	
	/**
	 * The maximum string length of y axis data
	 * @var int
	 */
	protected $maxlen_y;
	
	/**
	 * The minimum y value
	 * @var int
	 */
	protected $min_y;
	
	/**
	 * The maximum y value
	 * @var int
	 */
	protected $max_y;
	
	/**
	 * The used background color
	 * @var int
	 */
	protected $background_color;
	
	/**
	 * The used foreground color
	 * @var int
	 */
	protected $foreground_color;
	
	/**
	 * The used data color
	 * @var int
	 */
	protected $data_color;

	/**
	 * Constructs the chart
	 * @param int $width			width of the chart
	 * @param int $height			height of the chart
	 * @param string $title			title of the chart
	 * @param string $x_descr		x axis description
	 * @param string $y_descr		y axis description
	 * @param ChartStyle $style		the ChartStyle to use
	 * @param int $x_delta			[optional] distance between x axis labels
	 */
	public function __construct($width, $height, $title, $x_descr, $y_descr, LineChartStyle $style, $x_delta = 1)
	{
		$this->width = $width;
		$this->height = $height;
		$this->title = $title;
		$this->x_descr = $x_descr;
		$this->y_descr = $y_descr;
		$this->style = $style;
		$this->x_delta = $x_delta;
		
		$this->init();
	}
	
	/**
	 * Destroys the chart
	 */
	public function __destruct()
	{
		ImageDestroy($this->img);
	}

	/**
	 * Add a chart point. These must be added in ascending x order
	 * @param int $x		the x value of the point to add
	 * @param int $y		the y value of the point to add
	 */
	public function feed($x, $y)
	{
		// Check for new x maxlength
		if(!isset($this->maxlen_x) || strlen("".$x) > $this->maxlen_x) $this->maxlen_x = strlen("".$x);

		// Check for new y extrema
		if(!isset($this->min_y) || $this->min_y > $y) $this->min_y = floor($y);
		if(!isset($this->max_y) || $this->max_y < $y) $this->max_y = ceil($y);

		// Save data
		$this->data[] = array($x, $y);
	}
	
	/**
	 * Paints the chart and sends it to the client
	 */
	public function output()
	{
		$this->paint();
		header("Content-type: image/png");
		ImagePNG($this->img);
	}
	
	/**
	 * Initializes the chart picture
	 */
	protected function init()
	{
		// Create image
		$this->img = ImageCreate($this->width, $this->height);
		
		$style = $this->style;

		// Create background
		$bgcolor = $style->getBackgroundColor();
		$this->background_color = ImageColorAllocate($this->img, $bgcolor[0], $bgcolor[1], $bgcolor[2]);

		// Create foreground color
		$fgcolor = $style->getForegroundColor();
		$this->foreground_color = ImageColorAllocate($this->img, $fgcolor[0], $fgcolor[1], $fgcolor[2]);

		// Create data color
		$datacolor = $style->getDataColor();
		$this->data_color = ImageColorAllocate($this->img, $datacolor[0], $datacolor[1], $datacolor[2]);

		// X Axis
		ImageLine($this->img, $style->getAxisPaddingLeft(), $this->height - $style->getAxisDistanceX() - $style->getAxisPaddingBottom(), $this->width - $style->getAxisPaddingRight(), $this->height - $style->getAxisDistanceX() - $style->getAxisPaddingBottom(), $this->foreground_color);

		// Y Axis
		ImageLine($this->img, $style->getAxisDistanceY() + $style->getAxisPaddingLeft(), $style->getAxisPaddingTop(), $style->getAxisDistanceY() + $style->getAxisPaddingLeft(), $this->height - $style->getAxisPaddingBottom(), $this->foreground_color);

		// Title
		ImageString($this->img, $style->getTitleFontSize(), round(($this->width / 2) - (strlen($this->title) * ImageFontWidth($style->getTitleFontSize()) / 2)), $style->getTitleOffsetTop(), $this->title, $this->foreground_color);

		// X Description
		ImageString($this->img, $style->getAxisFontSize(), round($style->getAxisPaddingLeft() + $style->getAxisDistanceY() + (($this->width - $style->getAxisDistanceY() - $style->getAxisPaddingLeft() - $style->getAxisPaddingRight()) / 2) - (strlen($this->x_descr) * ImageFontWidth($style->getAxisFontSize()) / 2)), $this->height - $style->getXDescriptionOffsetBottom(), $this->x_descr, $this->foreground_color);

		// Y Description
		ImageStringUp($this->img, $style->getAxisFontSize(), $style->getYDescriptionOffsetLeft(), round($style->getAxisPaddingTop() + (($this->height - $style->getAxisDistanceX() - $style->getAxisPaddingTop() - $style->getAxisPaddingBottom()) / 2) + (strlen($this->y_descr) * ImageFontWidth($style->getAxisFontSize()) / 2)), $this->y_descr, $this->foreground_color);

		$this->data = array();
	}
	
	/**
	 * Paint the chart with the current data set
	 */
	protected function paint()
	{
		// Calculate max y stringlenght
		$this->maxlen_y = max(strlen("".$this->min_y), strlen("".$this->max_y));
		
		$style = $this->style;

		// Calculate scales
		$x_scale = ($this->width - $style->getAxisDistanceY() - $style->getAxisPaddingLeft() - $style->getAxisPaddingRight()) / (count($this->data) - 1);
		$y_scale = ($this->height - $style->getAxisDistanceX() - $style->getAxisPaddingTop() - $style->getAxisPaddingBottom()) / ($this->max_y - $this->min_y);

		// Y delta is calculated automatically
		$this->y_delta = floor(($this->max_y - $this->min_y) * $style->getAxisStrokeDistance() / ($this->height - $style->getAxisDistanceX() - $style->getAxisPaddingTop() - $style->getAxisPaddingBottom()));

		if($this->y_delta < 1) $this->y_delta = 1;

		// Calculate chart pixel origin
		$chartXOrigin = $style->getAxisDistanceY() + $style->getAxisPaddingLeft();
		$chartYOrigin = $this->height - $style->getAxisDistanceX() - $style->getAxisPaddingBottom();

		// Calculate chart pixel zero point
		$chartYZero = $chartYOrigin + round($this->min_y * $y_scale);

		// Draw x axis labels
		for($i=0;$i<count($this->data);$i++)
		{
			if($i % $this->x_delta == 0) // On every x_delta'th cycle
			{
				ImageString($this->img, $style->getAxisFontSize(), $chartXOrigin + round($i * $x_scale) - $this->maxlen_x * ImageFontWidth($style->getAxisFontSize()) + $style->getAxisXLabelOffsetX(), $chartYOrigin + $style->getAxisXLabelOffsetY(), sprintf("%" . $this->maxlen_x . "d", $this->data[$i][0]), $this->foreground_color);
				ImageLine($this->img, $chartXOrigin + round($i * $x_scale), $chartYOrigin, $chartXOrigin + round($i * $x_scale), $chartYOrigin + $style->getAxisLabelLineLength(), $this->foreground_color);
			}
		}

		// Draw y axis labels
		for($dataY = $this->min_y; $dataY <= $this->max_y; $dataY += $this->y_delta)
		{
			ImageString($this->img, $style->getAxisFontSize(), $chartXOrigin - $this->maxlen_y * ImageFontWidth($style->getAxisFontSize()) + $style->getAxisYLabelOffsetX(), $chartYOrigin - round(($dataY - $this->min_y) * $y_scale) + $style->getAxisYLabelOffsetY(), sprintf("%" . $this->maxlen_y . "d", $dataY), $this->foreground_color);
			ImageLine($this->img, $chartXOrigin, $chartYOrigin - round(($dataY - $this->min_y) * $y_scale), $chartXOrigin - $style->getAxisLabelLineLength(), $chartYOrigin - round(($dataY - $this->min_y) * $y_scale), $this->foreground_color);
		}

		// Get init points for the line
		$last_data = reset($this->data);
		$last_dataY = $last_data[1];

		// Draw line
		for($i=1;$i<count($this->data);$i++)
		{
			ImageLine($this->img, $chartXOrigin + round(($i-1) * $x_scale), $chartYZero - round($last_dataY * $y_scale), $chartXOrigin + round($i * $x_scale), $chartYZero - round($this->data[$i][1] * $y_scale), $this->data_color);

			$last_dataY = $this->data[$i][1];
		}
	}	
}
?>