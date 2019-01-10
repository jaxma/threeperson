form.verify({
    //系统大标出库
    stock_level: function(value, item) {
        if(!value) {
          return '请选择代理等级！'
        }
    },
    stock_agent_id: function(value, item) {
        if(!value) {
          return '请选择代理！'
        }
    },
    stock_templet_id: function(value, item) {
        if(!value) {
          return '请选择模板！'
        }
    },
})