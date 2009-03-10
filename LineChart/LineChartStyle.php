<?php
/**
 * Copyright (c) 2008-2009, Fabian "smf68" Hahn <smf68@smf68.ch>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace PhpFramework\LineChart;

use \PhpFramework\PhpFramework as PF;

/**
 * Collection of parameters used to style a LineChart.
 * If a LineChartStyle object is created and passed to a LineChart without changing anything, default values are used. 
 */
class LineChartStyle
{
	/**
	 * Distance the x axis has from the bottom border of the axis canvas
	 * @var int
	 */
	protected $axis_distance_x = 20;
	
	/**
	 * Distance the y axis has from the left border of the axis canvas
	 * @var int
	 */
	protected $axis_distance_y = 30;
	
	/**
	 * Left padding of the axis canvas from the chart picture
	 * @var int
	 */
	protected $axis_padding_left = 20;
	
	/**
	 * Right padding of the axis canvas from the chart picture
	 * @var int
	 */	
	protected $axis_padding_right = 5;
	
	/**
	 * Bottom padding of the axis canvas from the chart picture
	 * @var int
	 */	
	protected $axis_padding_bottom = 15;
	
	/**
	 * Top padding of the axis canvas from the chart picture
	 * @var int
	 */	
	protected $axis_padding_top = 20;
	
	
	/**
	 * The font size axis labels use
	 * @var int
	 */
	protected $axis_fontsize = 3;
	
	/**
	 * x offset of the x axis labels
	 * @var int
	 */
	protected $axis_xlabel_offX = 4;
	
	/**
	 * y offset of the x axis labels
	 * @var int
	 */
	protected $axis_xlabel_offY = 5;
	
	/**
	 * x offset of the y axis labels
	 * @var int
	 */
	protected $axis_ylabel_offX = -6;
	
	/**
	 * y offset of the y axis labels
	 * @var int
	 */
	protected $axis_ylabel_offY = -7;
	
	/**
	 * Length of axis label strokes
	 * @var int
	 */
	protected $axis_label_linelength = 3;
	
	/**
	 * Top offset of the title text
	 * @var int
	 */
	protected $title_offY = 3;
	
	/**
	 * Font size of the title
	 * @var int
	 */
	protected $title_fontsize = 5;
	
	/**
	 * Bottom offset of the x axis description
	 * @var int
	 */
	protected $xdescr_offY = 16;
	
	/**
	 * Left offset of the y axis description
	 * @var int
	 */
	protected $ydescr_offX = 3;
	
	/**
	 * Distance between two axis marker strokes
	 * @var int
	 */
	protected $delta_pixel = 28;
	
	/**
	 * Chart background color
	 * @var array[int]
	 */
	protected $background_color = array(255, 255, 255);
	
	/**
	 * Chart foreground color (descriptions)
	 * @var array[int]
	 */
	protected $foreground_color = array(0, 0, 0);
	
	/**
	 * Chart data color (line)
	 * @var array[int]
	 */
	protected $data_color = array(255, 0, 0);
	
	/**
	 * Sets the distance the x axis has from the bottom border of the axis canvas
	 * @param int $dx		x distance
	 */
	public function setAxisDistanceX($dx)
	{
		$this->axis_distance_x = $dx;
	}
	
	/**
	 * Sets the distance the y axis has from the left border of the axis canvas
	 * @param int $dy		y distance
	 */
	public function setAxisDistanceY($dy)
	{
		$this->axis_distance_y = $dy;
	}
	
	/**
	 * Sets the left padding of the axis canvas from the chart picture
	 * @param int $left		left padding
	 */
	public function setAxisPaddingLeft($left)
	{
		$this->axis_padding_left = $left;
	}
	
	/**
	 * Sets the right padding of the axis canvas from the chart picture
	 * @param int $right		right padding
	 */
	public function setAxisPaddingRight($right)
	{
		$this->axis_padding_right = $right;
	}
	
	/**
	 * Sets the bottom padding of the axis canvas from the chart picture
	 * @param int $bottom		bottom padding
	 */
	public function setAxisPaddingBottom($bottom)
	{
		$this->axis_padding_bottom = $bottom;
	}
	
	/**
	 * Sets the top padding of the axis canvas from the chart picture
	 * @param int $top		top padding
	 */
	public function setAxisPaddingTop($top)
	{
		$this->axis_padding_top = $top;
	}
	
	/**
	 * Sets the font size axis labels use
	 * @param int $size		font size
	 */
	public function setAxisFontSize($size)
	{
		$this->axis_fontsize = $size;
	}
	
	/**
	 * Sets x offset of the x axis labels
	 * @param int $dx		x offset
	 */
	public function setAxisXLabelOffsetX($dx)
	{
		$this->axis_xlabel_offX = $dx;
	}
	
	/**
	 * Sets y offset of the x axis labels
	 * @param int $dy		y offset
	 */
	public function setAxisXLabelOffsetY($dy)
	{
		$this->axis_xlabel_offY = $dy;
	}
	
	/**
	 * Sets x offset of the y axis labels
	 * @param int $dx		x offset
	 */
	public function setAxisYLabelOffsetX($dx)
	{
		$this->axis_ylabel_offX = $dx;
	}	
	
	/**
	 * Sets y offset of the y axis labels
	 * @param int $dy		y offset
	 */
	public function setAxisYLabelOffsetY($dy)
	{
		$this->axis_ylabel_offY = $dy;
	}
	
	/**
	 * Sets the length of axis label strokes
	 * @param int $length		stroke length
	 */
	public function setAxisLabelLineLength($length)
	{
		$this->axis_label_linelength = $length;
	}
	
	/**
	 * Sets the top offset of the title text
	 * @param int $dy		y offset
	 */
	public function setTitleOffsetTop($dy)
	{
		$this->title_offY = $dy;
	}

	/**
	 * Sets the font size of the title
	 * @param int $size		title fontsize
	 */
	public function setTitleFontSize($size)
	{
		$this->title_fontsize = $size;
	}
	
	/**
	 * Sets the bottom offset of the x axis description
	 * @param int $dy		bottom offset
	 */
	public function setXDescriptionOffsetBottom($dy)
	{
		$this->xdescr_offY = $dy;
	}
	
	/**
	 * Sets the left offset of the y axis description
	 * @param int $dx		left offset
	 */
	public function setYDescriptionOffsetLeft($dx)
	{
		$this->ydescr_offX = $dy;
	}
	
	/**
	 * Sets the distance between two axis marker strokes
	 * @param int $distance		stroke distance
	 */
	public function setAxisStrokeDistance($distance)
	{
		$this->delta_pixel = $distance;
	}
	
	/**
	 * Sets the chart background color
	 * @param array[int] $rgb		rgb background color
	 */
	public function setBackgroundColor($rgb)
	{
		$this->background_color = $rgb;
	}
	
	/**
	 * Sets the chart foreground color (descriptions)
	 * @param array[int] $rgb		rgb foreground color
	 */
	public function setForegroundColor($rgb)
	{
		$this->foreground_color = $rgb;
	}

	/**
	 * Sets the chart data color (line)
	 * @param array[int] $rgb		rgb data color
	 */
	public function setDataColor($rgb)
	{
		$this->data_color = $rgb;
	}
	
	/**
	 * Gets the distance the x axis has from the bottom border of the axis canvas
	 * @return int	x distance
	 */
	public function getAxisDistanceX()
	{
		return $this->axis_distance_x;
	}
	
	/**
	 * Gets the distance the y axis has from the left border of the axis canvas
	 * @return int	y distance
	 */
	public function getAxisDistanceY()
	{
		return $this->axis_distance_y;
	}
	
	/**
	 * Gets the left padding of the axis canvas from the chart picture
	 * @return int	left padding
	 */
	public function getAxisPaddingLeft()
	{
		return $this->axis_padding_left;
	}
	
	/**
	 * Gets the right padding of the axis canvas from the chart picture
	 * @return int	right padding
	 */
	public function getAxisPaddingRight()
	{
		return $this->axis_padding_right;
	}
	
	/**
	 * Gets the bottom padding of the axis canvas from the chart picture
	 * @return int	bottom padding
	 */
	public function getAxisPaddingBottom()
	{
		return $this->axis_padding_bottom;
	}
	
	/**
	 * Gets the top padding of the axis canvas from the chart picture
	 * @return int	top padding
	 */
	public function getAxisPaddingTop()
	{
		return $this->axis_padding_top;
	}
	
	/**
	 * Gets the font size axis labels use
	 * @return int	font size
	 */
	public function getAxisFontSize()
	{
		return $this->axis_fontsize;
	}
	
	/**
	 * Gets x offset of the x axis labels
	 * @return int	x offset
	 */
	public function getAxisXLabelOffsetX()
	{
		return $this->axis_xlabel_offX;
	}
	
	/**
	 * Gets y offset of the x axis labels
	 * @return int	y offset
	 */
	public function getAxisXLabelOffsetY()
	{
		return $this->axis_xlabel_offY;
	}
	
	/**
	 * Gets x offset of the y axis labels
	 * @return int	x offset
	 */
	public function getAxisYLabelOffsetX()
	{
		return $this->axis_ylabel_offX;
	}	
	
	/**
	 * Gets y offset of the y axis labels
	 * @return int	y offset
	 */
	public function getAxisYLabelOffsetY()
	{
		return $this->axis_ylabel_offY;
	}
	
	/**
	 * Gets the length of axis label strokes
	 * @return int	stroke length
	 */
	public function getAxisLabelLineLength()
	{
		return $this->axis_label_linelength;
	}
	
	/**
	 * Gets the top offset of the title text
	 * @return int	y offset
	 */
	public function getTitleOffsetTop()
	{
		return $this->title_offY;
	}

	/**
	 * Gets the font size of the title
	 * @return int	title fontsize
	 */
	public function getTitleFontSize()
	{
		return $this->title_fontsize;
	}
	
	/**
	 * Gets the bottom offset of the x axis description
	 * @return int	bottom offset
	 */
	public function getXDescriptionOffsetBottom()
	{
		return $this->xdescr_offY;
	}
	
	/**
	 * Gets the left offset of the y axis description
	 * @return int	left offset
	 */
	public function getYDescriptionOffsetLeft()
	{
		return $this->ydescr_offX;
	}
	
	/**
	 * Gets the distance between two axis marker strokes
	 * @return int	stroke distance
	 */
	public function getAxisStrokeDistance()
	{
		return $this->delta_pixel;
	}
	
	/**
	 * Gets the chart background color
	 * @return array[int]	rgb background color
	 */
	public function getBackgroundColor()
	{
		return $this->background_color;
	}
	
	/**
	 * Gets the chart foreground color (descriptions)
	 * @return array[int]	rgb foreground color
	 */
	public function getForegroundColor()
	{
		return $this->foreground_color;
	}

	/**
	 * Gets the chart data color (line)
	 * @return array[int]	rgb data color
	 */
	public function getDataColor()
	{
		return $this->data_color;
	}	
}
?>