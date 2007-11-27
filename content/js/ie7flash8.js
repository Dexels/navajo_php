// last update 22.03.2006

function show_flash(src, movie, width, height, wmode)
{
	document.write("<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\" width=\"" +width+ "\" height=\"" +height+ "\" id=\"" +movie+ "\">");
	document.write("<param name=\"movie\" value=\"" +src+ "\" />");
	document.write("<param name=\"wmode\" value=\"" +wmode+ "\" />");
	document.write("<embed src=\"" +src+ "\" wmode=\"" +wmode+ "\" width=\"" +width+ "\" height=\"" +height+ "\" name=\"" +movie+ "\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />");
	document.write("</object>");
}