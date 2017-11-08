<?php

class CSS_GENERATOR
{
		public function help()
	{
		echo "
		NAME
					css_generator – sprite generator for HTML use\n
		SYNOPSIS
					css_generator [OPTIONS]... assets_folder\n
		DESCRIPTION
					my_spriteenate all PNG images inside a folder in one sprite and write a
					stylesheet ready to use.
					Mandatory arguments to long options are mandatory for short options too.\n
		-r, --recursive
					Look for images into the assets_folder passed as arguement and all of
					its subdirectories.\n
		-i, --output-image=IMAGE
					Name of the generated image. If blank, the default name is
					« sprite.png ».\n
		-s, --output-style=STYLE
					Name of the generated stylesheet. If blank, the default name is
					« style.css »\n
		BONUS OPTIONS\n
		-p, --padding=NUMBER
					Add padding between images of NUMBER pixels\n
		-o, --override-size=SIZE
					Force each images of the sprite to fit a size of SIZExSIZE pixels\n
		-c, --columns_number=NUMBER
					The maximum number of elements to be generated horizontally. \n";

	}
	public static function option($option)
	{
		if(is_dir($option["folder"]))
		{
			if(isset($option["r"]) || isset($option["recursive"]))
			{
				self::my_recursive($option["folder"], $size);
			}
			else
			{
				$size = array();
				foreach(glob($option["folder"]."/"."*.png") as $value)
				{
					$size[] = $value;
				}
			}
			
			$finfo = finfo_open(FILEINFO_MIME_TYPE);

			foreach($size as $value)
			{
				if(finfo_file($finfo, $value) == "image/png")
				{
					$nlist[] = $value;
				}
			}

			finfo_close($finfo);

			self::size($option, $nlist);
		}
		elseif($option["folder"] == "css_generator.php")
		{
			echo "Need a valid folder as argument. ? --help\n";
		}
		else
		{
			echo "Not a valid folder. ? --help\n";
		}
	}

	public static function my_recursive($folder, &$size = array())
	{

		$path = opendir($folder);

		while(false !== ($file = readdir($path)))
		{
			if($file != "." && $file != ".." && is_dir($folder."/".$file))
			{
				self::my_recursive($folder."/".$file, $size);
			}
			elseif($file != "." && $file != ".." && is_file($folder."/".$file))
			{
				$size[] = $folder."/".$file;
			}
		}
		closedir($path);

		return $size;

	}

	public static function my_sprite($img, $path, $size, $option)
	{
		$i = 0;
		$largeur = 0;
		while($i <= count($size) - 1)
		{
			$imagecreate = imagecreatefrompng($path[$i]);
			imagecopy($img, $imagecreate, $largeur, 0, 0, 0, $size[$i][0], $size[$i][1]);
			$largeur += $size[$i][0];
			$i++;
		}

		if(isset($option["i"]))
			{
				$pngname = $option["i"].".png";
			}
		elseif(isset($option["output-image"]))
			{
				$pngname = $option["output-image"].".png";
			}
		else
			{
				$pngname = "sprite.png";
			}

		imagepng($img, $pngname);
		echo $i++." file concated in .png"."\n".$i++." file add to .css"."\n";

		self::my_css($path, $size, $pngname, $option);

	}

	public static function my_css($path, $size, $pngname, $option)
	{
				$css = ".sprite"." { 
				background-image: url(".$pngname.");
				background-repeat: no-repeat;
				display: block; 
				}\n";
			$position = 0;
			$i = 0;
			$ii = 0;

		while($i < count($size))
		{	// affecte la valeur
			$position += $size[$i][0];
			// affecte la valeur
			$css = $css .="
			".".".substr((substr($path[$ii], strrpos($path[$ii], "/") + 1)), 0, -4) . " {
				width: ".$size[$ii][0]."px;
				height: ".$size[$ii][1]."px;
				background-position: " .$position."px "."-5px" . ";
			}\n";
			$i++;
			$ii++;
		}

		if(isset($option["s"]))
			{
				file_put_contents($option["s"].".css", $css);
			}
		elseif(isset($option["output-style"]))
			{
				file_put_contents($option["output-style"].".css", $css);
			}
		else
			{
				file_put_contents("style.css", $css);
			}
	}


	public static function size($option, $size)
	{
		static $width = 0;
		$height = 0;
		$i = 0;

		foreach($size as $value)
		{
			$pstimg[] = getimagesize($value);
			$width += $pstimg[$i][0];
			if($height < $pstimg[$i][1])
			{
				$height = $pstimg[$i][1];
			}
			$i++;
		}

		$img = imagecreatetruecolor($width, $height);
		$noir = imagecolorallocate($img, 0, 0, 0);
		imagecolortransparent($img, $noir);

		self::my_sprite($img, $size, $pstimg, $option);
	}
}