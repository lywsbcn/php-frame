<?php
namespace jframe\JImage;


Class JImageInfo {

    const IMAGE_TYPE_GIF  = 1;

    const IMAGE_TYPE_JPEG = 2;

    const IMAGE_TYPE_PNG  = 3;

    public $path;
    /**图片 宽度 */
    public $width;

    /**图片 高度 */
    public $height;

    /**图片 类型 */
    public $type;

    /**图片 文件大小 */
    public $size;

    public $mime;



    static function Info($path){

        $im = new JimageInfo();
        $im->path = $path;
        return $im->getInfo();
    }


    private $image;

    public function getInfo(){
        
        if(file_exists($this->path)){
            $this->image = getImageSize($this->path);

            $this->size = filesize($this->path);

            if($this->image){
                $this->width = $this->image[0];
                $this->height= $this->image[1];
                $this->type  = $this->image[2];
                $this->mime  = $this->image['mime'];
            }

            return $this;
        }
        return NULL;
        
    }

    public function getImage(){
        switch($this->type){
            case self::IMAGE_TYPE_JPEG:
                return imagecreatefromjpeg($this->path);
            case self::IMAGE_TYPE_GIF:
                return imagecreatefromgif($this->path);
            case self::IMAGE_TYPE_PNG:
                return imagecreatefrompng($this->path);
            default:
                return NULL;
        }
    }

}