<?php
namespace Adumskis\LaravelAdvert;


use Adumskis\LaravelAdvert\Model\Advert;
use Adumskis\LaravelAdvert\Model\AdvertCategory;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;

class AdvertManager {

    /**
     * @var object;
     */
    private static $instance;

    /**
     * @return AdvertManager
     */
    public static function getInstance()
    {
        return static::$instance ?: (static::$instance = new self());
    }


    /**
     * Search advert by AdvertCategory type
     * If duplicate set to true then it's possible that advert will be the same with
     * previous showed advert
     *
     * @param $type
     * @param bool $duplicate
     * @return HtmlString|string
     */
    public function getHTML($type, $render_all = false){
        $advert_category = AdvertCategory::where('type', $type)->first();
        if(!$advert_category){
            return '';
        }

        $adverts = $advert_category
            ->adverts()
            ->where('active', true)
            ->inRandomOrder()->get();
        
        $html = '';
        foreach($adverts as $advert){
            $advert->plusViews();
            $advert->updateLastViewed();

            $html .= View::make('partials.advert', compact('advert'))->render();
            $html .= '<br>';

            if(!$render_all)
                break;
        }

        return $html;
    }

}

