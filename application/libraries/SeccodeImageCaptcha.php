<?php
/**
 * 验证码类
 * @author DV
 *
 */

class SeccodeImageCaptcha
{
	private $_fontLibrary = array(
		'cn' => '的一是在了不和有大这主中人上为们地个用工时要动国产以我到他会作来分生对于学下级就年阶义发成部民可出能方进同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批如应形想制心样干都向变关点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培着河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑', 
		
		'en' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 
	);
	
	private $_textLen;
	private $_lang;
	private $_config;
	private $_seccode;

	public function __construct()
	{
	
	}
	
	public function init($config, $textLen = 4, $lang = 'en')
	{
		$this -> _textLen = $textLen;
		$this -> _lang = $lang;
		$config['textlength'] = $textLen;
		$config['lang'] = $lang;
		$this -> _config = $config;
	}
	
	public function createSeccode()
	{
		$len = $this -> _lang == 'en' ? 1 : 3;
		$fontLibrary = $this -> _fontLibrary[$this -> _lang];
			
		$libraryLen = floor( strlen($fontLibrary) / $len );
		
		$this -> _seccode = '';
		for ($i = 0; $i < $this -> _textLen; $i++) {
			$this -> _seccode .= substr($fontLibrary, mt_rand(0, $libraryLen - 1) * $len, $len);
		}
		return $this -> _seccode;
	}
	
	public function output()
	{
		$img = new ImageCaptcha($this -> _config, $this -> _seccode);
		$img -> createImage();
	}
}


class ImageCaptcha
{
	private $_height;
	private $_width;
	private $_textNum; 
	
	private $_fontColor;
	private $_randFontColor; 
	private $_fontSize;
	private $_bgColor;
	private $_randBgColor;
	private $_textLang;
	private $_noisePoint;
	private $_noiseLine;
	private $_distortion;
	private $_distortionImage;
	private $_showBorder;
	private $_image;
	private $_fontpath;
	private $_charset;
	private $_contentRandomColor;
	
	
	public $textContent;
	
	public function imageCaptcha($config, $seccode)
	{
		$this -> _width = $config['width'];
		$this -> _height = $config['height'];
		$this -> _textNum = empty( $config['textlength'] ) ? mt_rand(3, 6) : $config['textlength'];
		$this -> _fontColor = $config['textcolor'] ? sscanf($config['textcolor'], '#%2x%2x%2x'):'';
		$this -> _fontSize = $config['textfontsize'];
		$this -> _textLang = $config['lang'];
		$this -> _bgColor = $config['bgcolor'] ? sscanf($config['bgcolor'], '#%2x%2x%2x'):'';
		$this -> _noisePoint = $config['noisepoint'];
		$this -> _noiseLine = $config['noiseline'];
		$this -> _distortion = $config['distortion'];
		$this -> _fontpath = $config['fontpath'];
		$this -> _charset = $config['charset'];
		$this -> _contentRandomColor = $config['contentrandomcolor'];
		
		$this -> textContent = $seccode;

	}

	/**
	 * 
	 * 初始化验证码图片
	 */
	private function _initImage() {   
		if (empty($this -> _width)) {
			$this -> _width = floor($this -> _fontSize*1.3)*$this -> _textNum+20;
		}
		
		if (empty($this -> _height))	{
			$this -> _height = floor($this -> _fontSize*2.3);
		}
		
		$this -> _image = imagecreatetruecolor($this -> _width, $this -> _height);
		
		if (empty($this -> _bgColor)) {
			$this -> _randBgColor = imagecolorallocate($this -> _image, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
		} else {
			$this -> _randBgColor = imagecolorallocate($this -> _image, $this -> _bgColor[0], $this -> _bgColor[1], $this -> _bgColor[2]);
		}
		
		imagefill($this -> _image, 0, 0, $this -> _randBgColor);
	}

	/**
	 * 
	 * 输出文字到验证码
	 */
	private function _createText() {    
		
		$pre_len = ($this -> _textLang == 'cn') ? 3 : 1;
		if ($this -> _textLang == 'cn' && strcasecmp($this -> _charset, 'utf-8')!= 0)
		{
			$this -> textContent = iconv('utf-8', $this -> _charset, $this -> textContent);
			$pre_len = 2;
		}

		if(empty($this -> _fontColor)) {
			$this -> _randFontColor = imagecolorallocate($this -> _image, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
		} else {
			$this -> _randFontColor = imagecolorallocate($this -> _image, $this -> _fontColor[0], $this -> _fontColor[1], $this -> _fontColor[2]);
		}
		
		$font = $this -> _getfontFamily();
		if(empty($font)) exit();
		
		$fontdir  = $this -> _fontpath . '/' . $this -> _textLang.'/';
		
		for($i = 0;$i < $this -> _textNum; $i++)
		{
			$this -> _fontFamily = $fontdir.$font[array_rand($font, 1)];
			
			//每个文字颜色都随机
			if ( ($this -> _contentRandomColor)
				 && ( empty($this -> _fontColor) ) 
			){
				$this -> _randFontColor = imagecolorallocate($this -> _image, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
			} else {
				$this -> _randFontColor = imagecolorallocate($this -> _image, $this -> _fontColor[0], $this -> _fontColor[1], $this -> _fontColor[2]);
			}
			
			imagettftext($this -> _image, 
							$this -> _fontSize, 
							mt_rand(-20, 20), 
							($i * ($this -> _fontSize + 1)) + ($this -> _width/$this -> _textNum) - floor($this -> _fontSize / 2), 
							floor($this -> _height / 2 + $this -> _fontSize / 2), 
							$this -> _randFontColor, 
							$this -> _fontFamily, 
							substr($this -> textContent, $i * $pre_len, $pre_len)
						);
		}
	}
	
	/**
	 * 
	 * 生成干扰点
	 */
	private function _createNoisePoint() {    
		for($i = 0; $i < $this -> _noisePoint; $i++) {
			imagesetpixel($this -> _image, mt_rand(0, $this -> _width), mt_rand(0, $this -> _height), $this -> _randFontColor);
		}
	}
	
	/**
	 * 
	 * 获取字体
	 */
	private function _getfontFamily(){    
		$fontdir  = $this -> _fontpath . '/' . $this -> _textLang . '/';

		if($handle = @opendir($fontdir))
		{
			$i = 0;
			while(false !== ($file = @readdir($handle))){
				if(strcasecmp(substr($file, -4), '.ttf') === 0) {
					$list[] =  $file;
					$i++;
				}
			}
		}	
		return $list;
	}
	
	/**
	 * 
	 * 产生干扰线
	 */
	private function _createNoiseLine()	{    
		for($i = 0;$i<$this -> _noiseLine;$i++)
		{
			imageline($this -> _image, 0, mt_rand(0, $this -> _width), $this -> _width, mt_rand(0, $this -> _height), $this -> _randFontColor);
		}
	}
	
	/**
	 * 
	 * 扭曲文字
	 */
	private function _distortionText() {    
		$this -> _distortionImage = imagecreatetruecolor($this -> _width, $this -> _height);
		imagefill($this -> _distortionImage, 0, 0, $this -> _randBgColor);
		for($x = 0;$x<$this -> _width;$x++)	{
			for($y = 0;$y<$this -> _height;$y++){
				$rgbColor = imagecolorat($this -> _image, $x, $y);
				imagesetpixel($this -> _distortionImage, (int)($x+sin($y/$this -> _height*2*M_PI-M_PI*0.5)*3), $y, $rgbColor);
			}
		}
		$this -> _image = $this -> _distortionImage;
	}
	
	
	/**
	 * 
	 * 生成验证码图片
	 */
	public function createImage() {    

		
		//创建基本图片
		$this -> _initImage(); 
		
		//输出验证码字符
		$this -> _createText(); 
		
		//产生干扰点
		$this -> _createNoisePoint(); 
		
		//产生干扰线
		$this -> _createNoiseLine(); 
		
		//扭曲文字
		if($this -> _distortion == '1'){
			$this -> _distortionText();
		}
		
		//添加边框
		if ($this -> _showBorder) {
			$color = ImageColorAllocate($this -> _image, $this -> _showBordercolor[0], $this -> _showBordercolor[1], $this -> _showBordercolor[2]);
			imagerectangle($this -> _image, 0, 0, $this -> _width-1, $this -> _height-1, $color);
		} 
		
		
		header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
		// HTTP/1.1
		header('Cache-Control: private, no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check = 0, pre-check = 0, max-age = 0', false);
		// HTTP/1.0
		header('Pragma: no-cache');
		header("Content-type:image/png"); 
		
		imagepng($this -> _image);
		imagedestroy($this -> _image);
		
		if ($this -> _distortion != false) {
			imagedestroy($this -> _distortionImage);
		}
		
	}
}