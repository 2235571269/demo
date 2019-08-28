<?php 
namespace app\controllers;
use yii\web\Controller;
use yii;

/**
* 
*/
class SiteController extends Controller
{
	/**
	 * 首页展示qq登录
	 */
	public function actionIndex()
	{
		return $this->render('index');
	}
	/**
	 * 调用腾讯的qq登录页
	 */
	public function actionQq_login()
	{
		//实例化
		$login=new \QC();
		//跳转到qq登录
		$login->qq_login();
	}
	/**
	 * 回调函数
	 */
	public function actionBack()
	{
		//实例化
		$qq=new \QC();
		//获取access_tocken和openid
		$access_tocken=$qq->qq_callback();
		$openid=$qq->get_openid();
		//实例化 并且传入access_tocken和openid
		$qq=new \QC($access_tocken,$openid);
		$userinfo=$qq->get_user_info();

		// print_r($userinfo);
		echo '头像：<img src="'.$userinfo['figureurl_1'].'" alt=""><br>';
		echo '用户名：'.$userinfo['nickname'].'<br>';
		echo '性别：'.$userinfo['gender'].'<br>';
		echo '城市：'.$userinfo['province'].'<br>';
		
	}

	public function actionWeather()
	{
		return $this->render('weather');
	}
	//查询天气  入库
	public function actionWeather_do(){
		//接收条件
		$where=\Yii::$app->request->post('site');
		//获取天气数据

		 $redis = new \Redis();
		 $redis->connect('127.0.0.1', 6379);
		 if ($redis->hexists('data',"where_$where")) {
		 	$data=json_decode($redis->hget('data',"where_$where"),true);
		 	$data['redis']='缓存';
		 } else {
		 	$json=file_get_contents("http://api.k780.com:88/?app=weather.future&weaid=$where&&appkey=43797&sign=2c959d4bd8e6a384b98ad35aff83dcc2&format=json");
		$arr=json_decode($json,true);

		// print_r($arr);
		//循环处理要展示的数据
		foreach ($arr['result'] as $key => $value) {
			$data['days'][]=$value['days'];
			$data['temp'][]=[(int)$value['temp_low'],(int)$value['temp_high']];

			//$arr2为入库数据
			$arr2[$key]['site']=$where;
			$arr2[$key]['week']=$value['week'];
			$arr2[$key]['low']=$value['temp_low'];
			$arr2[$key]['high']=$value['temp_high'];
		}
		//数据入库
		$connection=\Yii::$app->db;
		$connection->createCommand()->batchInsert('weather', ['site', 'week','low','high'], $arr2)->execute();


		
		$data['where']=$where;

		$redis->hset('data',"where_$where",json_encode($data));
		$data['redis']='查询';
		 }

//调用百度接口获取坐标
$map=file_get_contents("http://api.map.baidu.com/geocoding/v3/?address=$where&output=json&ak=y01uOWNlWjsUG980LN2O849eIotOuzGA&callback=showLocation");
		$map=substr($map, 27,-1);
		$mapArr=json_decode($map,true);
		// print_r($mapArr);die;
		$point['lng']=(float)$mapArr['result']['location']['lng'];
		$point['lat']=(float)$mapArr['result']['location']['lat'];

		
		
		
		// print_r($data);
		return $this->render('show',['data'=>$data,'point'=>$point]);

	}
}
 ?>