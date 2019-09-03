<?php
namespace jframe\JImage;
use jframe\JImage\JImageInfo;

Class JImage {

    public $saveType = 0;

    /**调整后的宽度 */
    public $width=0;
    /**调整后的高度 */
    public $height=0;

    /**设置调整后的尺寸 */
    public function setSize($width,$height){
        $this->width = $width;
        $this->height= $height;
    }

    /**设置原图路径 */
    private $sourcePath="";
    /**原图信息,如果路径不存在 NUll */
    private $im;
    public function setSource($path){
        $this->sourcePath= $path;
        $this->im = JImageInfo::Info($path);
    }


    /**等比缩放 */
    const ResizeGeometricScaling=1;
    /**图片拉伸 */
    const ResizeTypeStretch = 2;
    /** 居中裁剪*/
    const ResizeCenterOfCutting=3;
    /** 顶部裁剪*/
    const ResizeTopOfCutting=4;

    public $resizeType = self::ResizeGeometricScaling;    

   
    private $savePath;
    public function save($path=''){
        if(empty($path)){
            $this->savePath = $this->sourcePath;
        }else{
            $this->savePath= $path;
        }

        if(!$this->im) return;
        switch($this->resizeType){
            case self::ResizeGeometricScaling:
                $this->GeometricScaling();
                break;
            case self::ResizeTypeStretch:
                $this->Stretch();
                break;
            case self::ResizeCenterOfCutting:
                $this->CenterOfCutting();
                break;
            case self::ResizeTopOfCutting:
                $this->TopOfCutting();
                break;
            default:
                break;
            
        }
    }
    public function autocondense($path=''){       


        if($this->width >= $this->im->width && $this->height >= $this->im->height){
            return;
        }

        $this->save($path);
    }



    /**图片拉伸 */
    private function Stretch(){
        $this->imageCopy(0,0,$this->width,$this->height,0,0,$this->im->width,$this->im->height);
    }
    /**图片裁剪 */
    private function TopOfCutting(){

        $tw = $this->width > $this->im->width ? $this->im->width:$this->width;
        $th = $this->height>$this->im->height ?$this->im->height:$this->height;


        $this->imageCopy(0,0,$tw,$th,0,0,$tw,$th);
    }

    /** 居中裁剪*/
    private function CenterOfCutting(){
         /* 获取图像尺寸信息 */
        $target_w = $this->width;
        $target_h = $this->height;
        $source_w = $this->im->width;
        $source_h = $this->im->height;
        /* 计算裁剪宽度和高度 */
        $judge = (($source_w / $source_h) > ($target_w / $target_h));
        $resize_w = $judge ? ($source_w * $target_h) / $source_h : $target_w;
        $resize_h = !$judge ? ($source_h * $target_w) / $source_w : $target_h;
        $start_x = $judge ? ($resize_w - $target_w) / 2 : 0;
        $start_y = !$judge ? ($resize_h - $target_h) / 2 : 0;
        /* 绘制居中缩放图像 */
        $resize_img = imagecreatetruecolor($resize_w, $resize_h);
        imagecopyresampled($resize_img, $this->im->getImage(), 0, 0, 0, 0, $resize_w, $resize_h, $source_w, $source_h);
        $target_img = imagecreatetruecolor($target_w, $target_h);
        imagecopy($target_img, $resize_img, 0, 0, $start_x, $start_y, $resize_w, $resize_h);
        $this->imageBuild($target_img);
    }


    /**等比缩放 */
    private function GeometricScaling(){
        $tw = $this->width;
        $th = $this->height;
        if($this->im->width<$this->im->height){
            $tw = ($this->height/$this->im->height) * $this->im->width;
        }else{
            $th = ($this->width/$this->im->width) * $this->im->height;
        }
        $this->imageCopy(0,0,$tw,$th,0,0,$this->im->width,$this->im->height);
    }

    private function imageCopy($tx,$ty,$tw,$th,$sx,$sy,$sw,$sh){
        $tn= imagecreatetruecolor($tw,$th);
        imagecopyresampled($tn,$this->im->getImage(),$tx,$ty,$sx,$sy,$tw,$th,$sw,$sh);
        $this->imageBuild($tn);
    }

    private function imageBuild($tagerImage){
        $type = $this->saveType == 0 ? $this->im->type:$this->saveType;
        switch($type){
            case JImageInfo::IMAGE_TYPE_JPEG:
                imagejpeg($tagerImage,$this->savePath);
                break;
            case JImageInfo::IMAGE_TYPE_GIF:
                imagegif($tagerImage,$this->savePath);
                break;
            case JImageInfo::IMAGE_TYPE_PNG:
                imagepng($tagerImage,$this->savePath);
                break;

        }
    }   
}

