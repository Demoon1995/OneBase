<?php

namespace app\admin\controller;

class Config extends AdminBase
{
    
    public $model;
    
    //基类初始化
    public function _initialize()
    {
        
        parent::_initialize();
        
        $this->model =  model('Config');
    }
    
    /**
     * 配置管理
     */
    public function index()
    {
        
        //获取配置数据
        $data = $this->model->getConfigList($this->param);
        
        $this->assign('group', $data['config_group_list']);
        $this->assign('list', $data['list']);
        $this->assign('group_id', $data['group_id']);
        $this->assign('name', $data['name']);
        $this->assign('meta_title', '配置管理');
        
        return $this->fetch();
    }
    
    //后台配置设置
    public function group()
    {
        if (empty($this->param['id'])) {
            
            $this->param['id'] = 1;
        }
        
        $config_group_list = parse_config(3, config('config_group_list'));
        
        $config_model = db('config');

        $list = $config_model->where(array('status' => 1,'group' => $this->param['id']))->field('id, name, title, extra, value, remark, type')->order('sort')->select();
        
        $this->assign('list', $list);
        $this->assign('id', $this->param['id']);
        $this->assign('meta_title', $config_group_list[$this->param['id']].'设置');
        $this->assign('config_group_list', $config_group_list);
        
        return $this->fetch();
    }
    
    /**
     * 批量配置设置
     */
    public function save($config)
    {
        
        if ($config && is_array($config)) {
            
            $config_model = db('config');
            
            foreach ($config as $name => $value) {
                
                $map = array('name' => $name);
                
                $config_model->where($map)->setField('value', $value);
            }
        }
        
        $this->success('保存成功！');
    }
    
    
    
    
    
    
    
/**
 * 编辑配置信息
 */
public function edit()
{
    
    $data = $this->request->param();
    
    if (IS_POST) {
        
        $validate = validate('Config');
        $validate_result = $validate->check($data);

        if (!$validate_result) {
            $this->error($validate->getError());
        }
        
        $config_model = model('Config');

        if (!empty($data['id'])) {

            $res = $config_model->allowField(true)->save($data, array('id' => $data['id']));
        } else {
            
            unset($data['id']);
            $res = $config_model->allowField(true)->save($data);
        }
        
        if ($res) {

            $this->success('操作成功!', url('index'));
        } else {

            $this->error('操作失败!');
        }
        
        
    } else {
        
        $config_group_list = parse_config(3, config('config_group_list'));
        $config_type_list = parse_config(3, config('config_type_list'));
        
        
        $this->assign('config_group_list', $config_group_list);
        $this->assign('config_type_list', $config_type_list);
        
        if (!empty($data['id'])) {

            $config_model = model('Config');

            $info = $config_model->get($data['id']);

            $this->assign('meta_title', '编辑配置');

            $this->assign('info', $info);
        } else {

            $this->assign('meta_title', '新增配置');
        }

        return $this->fetch();
      
    }
}
    
    
}
