<?php
namespace app\qiangdan\logic;
use think\Model;
use think\Db;

class QiangLogic extends Model
{
	public function change_weina($order_id,$reason){
		$kd_order_model = M('kd_order');
		$order_sn = $kd_order_model ->where('order_id',$order_id)->getField('order_sn');
		$user_id = $kd_order_model ->where('order_id',$order_id)->getField('user_id');
		$consignee = $kd_order_model ->where('order_id',$order_id)->getField('consignee');
		$kuaidi_name = $kd_order_model ->where('order_id',$order_id)->getField('kuaidi_name');
		
		$openid = M('users') ->where('user_id',$user_id)->getField('openid');
		
		$access_token = access_token();
		$url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
		$json = array(
				'touser'=> $openid,
				'template_id'=>"Ox9KeFiYoBHP4lsWyjkfv5QXAffosGd-0-eAsO83hFU",
				'url'=>"http://v.yykddn.com/kuaidi/order/order_detail/id/".$order_id.".html",
				'data'=>array(
						'first'=>array(
								'value'=> "亲爱的".$consignee."
您的快递由于".$reason."，转成未拿",
								'color'=>"#DC143C"
						),
						'OrderSn'=>array(
								'value'=> $order_id,
								'color'=>"#000000"
						),
						'OrderStatus'=>array(
								'value'=>'转成未拿',
								'color'=>"#000000"
						),
						
						'remark'=>array(
								'value'=>"
未拿状态下，可以取消订单								
点击“详情”查看完整订单信息",
								'color'=>"#8B1A1A"
						)
				)
		);
		
		$json = json_encode($json);
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$out=curl_exec($ch);
		curl_close($ch);
		
	}
	
	
	
    //抢单后提醒下单用户
    public function push_msg_qiang($order_id){
        $first = "已接单
";
        $order_model = M('kd_order');
 
        $order_detail_url = "http://v.yykddn.com/kuaidi/order/order_detail/id/".$order_id.".html";

        $order = $order_model ->where('order_id',$order_id)->find();
    
        $user_id = $order['user_id'];
        $receiver = $order['receiver'];
         
        $openid = M('users') ->where('user_id',$user_id)->getField('openid');
        
        $name =  M('users_qiang') ->where('user_id',$receiver)->getField('name');
        $mobile =  M('users_qiang') ->where('user_id',$receiver)->getField('mobile');
    
        $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"wZeg9LlyZZbGHOc1TORwHDn0_EmO0VAaHvNQNX0QNiI",
            'url'=>$order_detail_url,
            'data'=>array(
                'first'=>array(
                    'value'=> $first,
                    'color'=>"#DC143C"
                ),
                'keyword1'=>array(
                    'value'=> $order_id,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=> $name.'（ID：'.$receiver.'）',
                    'color'=>"#000000"
                ),
                'keyword3'=>array(
                    'value'=> $mobile,
                    'color'=>"#000000"
                ),
    
                'remark'=>array(
                    'value'=>"
如有问题请点击菜单栏[客服咨询]
点击“详情”查看完整订单信息",
                    'color'=>"#8B1A1A"
                )
            )
        );
    
        $json = json_encode($json);
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $out=curl_exec($ch);
        curl_close($ch);
    
    }
    
    
    //送达后提醒下单用户
    public function push_msg_songda($order_id){
     
         
      $order_detail_url = "http://v.yykddn.com/kuaidi/order/order_detail/id/".$order_id.".html";
      $order = M('kd_order') ->where('order_id',$order_id)->find();
      
      $type_name = $order['kuaidi_name'];
   
        $user_id = $order['user_id'];
        $consignee =  $order['consignee'];
        $receiver_id =  $order['receiver'];
    
        $receiver = M('users_qiang')->where('user_id',$receiver_id)->find();
        
        $openid = M('users')->where('user_id',$user_id)->getField('openid');
        
        $receiver_name = $receiver['name'];
        $receiver_mobile = $receiver['mobile'];
      
        $access_token = access_token();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        $json = array(
            'touser'=> $openid,
            'template_id'=>"zrmJgx1HUi2eXlT-PKX_JzyvCvXokOt9fSaR7ZTxS5c",
            'url'=>$order_detail_url,
            'data'=>array(
                'first'=>array(
                    'value'=> "您的（".$type_name."快递）已送达

配送员：".$receiver_name."
电话：".$receiver_mobile,
                    'color'=>"#000000"
                ),
                'keyword1'=>array(
                    'value'=> $order_id,
                    'color'=>"#000000"
                ),
                'keyword2'=>array(
                    'value'=> $consignee,
                    'color'=>"#000000"
                ),
                'keyword3'=>array(
                    'value'=> '已送达',
                    'color'=>"#000000"
                ),
    
                'remark'=>array(
                    'value'=>"
如有问题请联系配送员               
投诉请点击菜单栏[客服咨询]",
                    'color'=>"#8B1A1A"
                )
            )
        );
    
        $json = json_encode($json);
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $out=curl_exec($ch);
        curl_close($ch);
    
        return $out;
    }
    
}