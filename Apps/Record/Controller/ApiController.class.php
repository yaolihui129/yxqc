<?php
/**
 * Created by PhpStorm.
 * User: yaolihui
 * Date: 2018/2/11
 * Time: 10:46
 */

namespace Record\Controller;
class ApiController extends CommonController
{
    function init()
    {
        $dict = array(
            'name' => ['Api', 'tp_apitest', 'tp_apiversion', 'tp_api_parameter', 'tp_api_scene'],
            'branch' => ['全部', '保险服务', '安鑫保', '车险APP', '易鑫车服', '微信', '第三方'],
            'agreement' => ['http', 'https'],
            'domain' => ['127.0.0.1', 'service.axb.chexian.com', 'cfw.taoche.com', 'axb.chexian.com'],
            'way' => ['GET', 'POST', 'HEAD'],
            'ways'=>['request','response'],
            'authority' => ['无', 'Auth', '签名'],
            'required' => ['是', '否'],
            'type' => ['Integer', 'String', 'Long','Object', 'Personal', 'Group', 'Date', 'Boolean', 'List', 'Decimal','ShippingAddressParam'],
        );
        foreach ($dict['branch'] as $k => $v) {
            if ($k) {
                $branch[$k]['key'] = $v;
                $branch[$k]['value'] = $v;
            }
        }
        foreach ($dict['agreement'] as $k => $v) {
            $agreement[$k]['key'] = $v;
            $agreement[$k]['value'] = $v;
        }
        foreach ($dict['domain'] as $k => $v) {
            $domain[$k]['key'] = $v;
            $domain[$k]['value'] = $v;
        }
        foreach ($dict['way'] as $k => $v) {
            $way[$k]['key'] = $v;
            $way[$k]['value'] = $v;
        }
        foreach ($dict['authority'] as $k => $v) {
            $authority[$k]['key'] = $v;
            $authority[$k]['value'] = $v;
        }
        foreach ($dict['required'] as $k => $v) {
            $required[$k]['key'] = $v;
            $required[$k]['value'] = $v;
        }
        foreach ($dict['type'] as $k => $v) {
            $type[$k]['key'] = $v;
            $type[$k]['value'] = $v;
        }
        foreach ($dict['ways'] as $k => $v) {
            $ways[$k]['key'] = $v;
            $ways[$k]['value'] = $v;
        }
        //封装返回数组
        $info = array(
            'name' => $dict['name'],
            'dict' => [
                'dict' => $dict,
                'branch' => $branch,
                'agreement' => $agreement,
                'domain' => $domain,
                'way' => $way,
                'authority' => $authority,
                'required' => $required,
                'type' => $type,
                'ways'=>$ways,
                ],

        );
        return $info;
    }

    public function index()
    {
        $info = $this->init();
        $_SESSION['branch'] = I('branch');
        $this->assign('branch', $info['dict']['dict']['branch']);
        if (I('id')) {
            $where = $_SESSION['where']['api'];
            $_SESSION['branch'] = $where['branch'];
            $this->assign('search', $_SESSION['search']['api']);
        } else {
            $search = trim(I('search'));
            $_SESSION['search']['api'] = $search;
            $this->assign('search', $search);
            if ($_SESSION['branch'] == '全部') {
                $_SESSION['branch'] = '';
            } else {
                if ($_SESSION['branch']) {
                    $where['branch'] = $_SESSION['branch'];
                }
            }
            $where['client|apiName|adress|author'] = array('like', '%' . $search . '%');
            $where['deleted'] = '0';
            $_SESSION['where']['api'] = $where;
        }

        $m = M($info['name'][1]);
        $data = $m->where($where)->order('branch,client,adress')->select();
        $this->assign('data', $data);
        $count = $m->where($where)->count();
        $this->assign('count', $count);

        //封装下拉列表
        $pingt = $this->select($info['dict']['branch'], 'branch', $_SESSION['branch']);
        $this->assign("pingt", $pingt);

        $agreement = $this->select($info['dict']['agreement'], 'agreement', 1);
        $this->assign("agreement", $agreement);

        $domain = $this->select($info['dict']['domain'], 'domain', $_SESSION['api']['domain']);
        $this->assign("domain", $domain);

        $way = $this->select($info['dict']['way'], 'way', 1);
        $this->assign("way", $way);

        $authority = $this->select($info['dict']['authority'], 'authority', $_SESSION['api']['authority']);
        $this->assign("authority", $authority);

        $this->assign("desc", PublicController::editor("desc", 'V1.0.0:'));

        $this->display();
    }

    function tianjapi(){
        /*
         * 1.添加一条API信息，返回id
         * 2.根据apiId,添加默认场景
         * 3.根据apiId,添加默认版本
         * */
        $info = $this->init();
        $_POST['adder'] = $_SESSION['account'];
        $_POST['moder'] = $_SESSION['account'];
        $_POST['ctime'] = time();
        //缓存常用上一次的字段
        $_SESSION['api']['client']=$_POST['client'];
        $_SESSION['api']['author']=$_POST['author'];
        $_SESSION['api']['domain']=$_POST['domain'];
        $_SESSION['api']['authority']=$_POST['authority'];

        $m = D($info['name'][1]);
        if (!$m->create()) {
            $this->error($m->getError());
        }
        //添加API数据返回apiID
        $api=$m->add();
        if ($api) {
            $_POST['api'] = $api;
            $_POST['version'] = 'V1.0.0:';
            //添加版本信息
            $table1 = D($info['name'][2]);
            if (!$table1->create()) {
                $this->error($table1->getError());
            }
            if($table1->add()){
                $_POST['api'] = $api;
                $_POST['scene'] = '正常请求';
                $_POST['desc'] = '按照示例请求接口';
                $_POST['level'] = 1;
                $_POST['sn'] = 1;
                //添加场景信息
                $table2 = D($info['name'][4]);
                if (!$table2->create()) {
                    $this->error($table2->getError());
                }
                if($table2->add()){
                    $this->success("添加成功");
                }
            }
        } else {
            $this->error("添加失败");
        }
    }
    function tianjcs(){
        $info = $this->init();
        //缓存上一次的分组
        $_SESSION['parameter']['group']=$_POST['group'];
        $_SESSION['parameter']['ways']=$_POST['ways'];
        $_POST['table']=$info['name'][3];
        $this->insert();
    }
    function tianjbanb(){
        /*
         * 1.添加一条版本信息
         * 2.更新说所有场景状态到6-版本更新（待确认）
         * */
        $info = $this->init();
        $_POST['adder'] = $_SESSION['account'];
        $_POST['moder'] = $_SESSION['account'];
        $_POST['ctime'] = time();
        //添加版本信息
        $table1 = D($info['name'][2]);
        $map=array('version'=>$_POST['version'],'api'=>$_POST['api'],'deleted'=>'0');
        $arr=$table1->where($map)->select();
        if($arr){
            $this->error("不能重复添加版本");
        }else{
            if (!$table1->create()) {
                $this->error($table1->getError());
            }
            if($table1->add()){
                $where=array('api'=>I('api'),'deleted'=>'0');
                $m=M($info['name'][4]);
                $data=$m->where($where)->select();
                foreach ($data as $da){
                    $a['id']=$da['id'];
                    $a['status']=6;
                    $a['moder'] = $_SESSION['account'];
                    $m->save($a);
                }
                $this->success("添加成功");
            }
        }

    }


    public function mod()
    {
        $info = $this->init();
        $arr = M($info['name'][1])->find(I('id'));
        $this->assign('arr', $arr);
        $where = $_SESSION['where']['api'];

        $data = M($info['name'][1])->where($where)->order('branch,adress')->select();
        $this->assign('data', $data);

        $this->assign("desc", PublicController::editor("desc", $arr['desc']));
        $this->assign("request", PublicController::editor("request", $arr['request']));
        $this->assign("response", PublicController::editor("response", $arr['response']));


        //封装下拉列表
        $pingt = $this->select($info['dict']['branch'], 'branch', $arr['branch']);
        $this->assign("pingt", $pingt);

        $agreement = $this->select($info['dict']['agreement'], 'agreement', $arr['agreement']);
        $this->assign("agreement", $agreement);

        $domain = $this->select($info['dict']['domain'], 'domain', $arr['domain']);
        $this->assign("domain", $domain);

        $way = $this->select($info['dict']['way'], 'way', $arr['way']);
        $this->assign("way", $way);

        $authority = $this->select($info['dict']['authority'], 'authority', $arr['authority']);
        $this->assign("authority", $authority);

        $this->display();
    }

    public function develop()
    {
        $info = $this->init();
        $m=D($info['name'][2]);
        $data =$m->find(I(id));
        $_POST['id']=$data['id'];
        $_POST['status']=2;
        $_POST['develop']=date('Y-m-d H:i:s',time());
        if ($m->save($_POST)) {
            $a['id']=$data['api'];
            $a['develop']=$data['version'];
            D($info['name'][1])->save($a);
            $this->success("提测成功！");
        } else {
            $this->error("提测失败！");
        }

    }
    function reject(){
        $info = $this->init();
        $m=D($info['name'][2]);
        $data =$m->find(I(id));
        if($data['status']==2){
            $_POST['status']=3;
            $_POST['id']=$data['id'];
            if ($m->save($_POST)) {
                $this->success("驳回成功！");
            } else {
                $this->error("驳回失败！");
            }
        }else{
            $this->error("版本不在提测状态，无法驳回！");
        }


    }
    function pass(){
        $info = $this->init();
        $m=D($info['name'][2]);
        $data =$m->find(I(id));
        if($data['status']==2){
            $_POST['status']=4;
            $_POST['id']=$data['id'];
            if ($m->save($_POST)) {
                $this->success("测试通过！");
            } else {
                $this->error("标记失败！");
            }
        }else{
            $this->error("版本不在提测状态，标记通过！");
        }
    }
    public function release()
    {
        $info = $this->init();
        $m=D($info['name'][2]);
        $data = $m->find(I(id));
        if($data['status']==4){
            $_POST['status']=5;
            $_POST['id']=$data['id'];
            $_POST['release']=date('Y-m-d H:i:s',time());
            if ($m->save($_POST)) {
                $a['id']=$data['api'];
                $a['release']=$data['version'];
                if(D($info['name'][1])->save($a)){
                    $this->success("预发成功！");
                }else{
                    $this->error("预发失败—1！");
                }
            } else {
                $this->error("预发失败-2！");
            }
        }else{
            $this->error("此版本尚未测试通过！");
        }

    }

    function cancel(){
        $info = $this->init();
        $m=D($info['name'][2]);
        $data = $m->find(I(id));
        $_POST['id']=$data['id'];
        $_POST['status']=7;
        if ($m->save($_POST)) {
            $this->success("取消成功！");
        }else{
            $this->error("取消失败");
        }
    }
    function online()
    {
        $info = $this->init();
        $m=D($info['name'][2]);
        $data = $m->find(I(id));
        $_POST['id']=$data['id'];
        if($data['status']==5){
            $_POST['status']=6;
            $_POST['online']=date('Y-m-d H:i:s',time());

            if ($m->save($_POST)) {
                $a['id']=$data['api'];
                $a['otime']=$_POST['online'];
                $a['online']=$data['version'];
                $a['desc'].=$data['desc'];
                if(D($info['name'][1])->save($a)){
                    $this->success("上线成功！");
                }else{
                    $this->error("上线失败—1！");
                }
            } else {
                $this->error("上线失败-2！");
            }
        }else{
            $this->error("此版本尚未预发布！");
        }

    }

    public function details()
    {
        $info = $this->init();
        $id = I(id);
        $data = M($info['name'][1])->find($id);
        $this->assign('data', $data);

        $where = array('api' => $id, 'deleted' => '0');
        $version = M($info['name'][2])->where($where)->order('version desc')->select();
        $this->assign('version', $version);

        $parameter = M($info['name'][3])->where($where)->order('sn,id')->select();
        $this->assign('parameter', $parameter);

        $scene = M($info['name'][4])->where($where)->order('sn,id')->select();
        $this->assign('scene', $scene);


        //参数添加
        $parameter = M($info['name'][3])->where($where)->count() + 1;
        $scene = M($info['name'][4])->where($where)->count() + 1;
        $version = M($info['name'][2])->where($where)->count() + 1;
        $c = array('parameter' => $parameter, 'scene' => $scene, 'version' => $version);

        $this->assign('c', $c);
        //封装下拉列表
        $type = $this->select($info['dict']['type'], 'type', 1);
        $this->assign("type", $type);

        $required = $this->select($info['dict']['required'], 'required', 1);
        $this->assign("required", $required);

        $ways= $this->select($info['dict']['ways'], 'ways', $_SESSION['parameter']['ways']);
//        dump($info['dict']);
        $this->assign("ways", $ways);

        //封装富文本编辑器
        $this->assign("parameterDesc", PublicController::editor("desc"));


        $this->display();
    }

    public function modparameter()
    {
        $info = $this->init();
        $arr = M($info['name'][3])->find(I('id'));
        $this->assign('arr', $arr);

        //封装富文本编辑器
        $this->assign("desc", PublicController::editor("desc", $arr['desc']));
        $required = $this->select($info['dict']['required'], 'required', $arr['required']);
        $this->assign("required", $required);

        $where = array('api' => I('api'), 'deleted' => '0');
        $parameter = M($info['name'][3])->where($where)->order('sn,id')->select();
        $this->assign('parameter', $parameter);
//        dump($parameter);
        $this->display();
    }

    public function modscene()
    {
        $info = $this->init();
        $arr = M($info['name'][4])->find(I('id'));
        $this->assign('arr', $arr);
        //封装富文本编辑器
        $this->assign("desc", PublicController::editor("desc", $arr['desc']));

//        $required = $this->select($info['dict']['required'], 'required', $arr['required']);
//        $this->assign("required", $required);

        $where = array('api' => I('api'), 'deleted' => '0');
        $scene = M($info['name'][4])->where($where)->order('sn,id')->select();
        $this->assign('scene', $scene);
//        dump($arr);
        $this->display();
    }

    public function modversion()
    {
        $info = $this->init();
        $arr = M($info['name'][2])->find(I('id'));
        $this->assign('arr', $arr);
        //封装富文本编辑器
        $this->assign("desc", PublicController::editor("desc", $arr['desc']));

        $where = array('api' => I('api'), 'deleted' => '0');
        $version = M($info['name'][2])->where($where)->order('version desc')->select();
        $this->assign('version', $version);

        $this->display();
    }

}