var _gaq = _gaq || [];			_gaq.push(['_setAccount', 'UA-35062786-3']);							_gaq.push(['_trackPageview',window.location]);			(function () {				var ga = document.createElement('script');				ga.type = 'text/javascript';				ga.async = true;				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';				var s = document.getElementsByTagName('script')[0];				s.parentNode.insertBefore(ga, s);			})();
var moduleTabs;
var module_factory_form;
var loadModParamForm;
jquery(document).ready(function(){
	loadModParamForm = new varienForm('mod_load_param_form');
	moduleFactoryForm= new varienForm('module_create_form');
	moduleTabs= new varienTabs('module_info_tabs', 'module_create_form', 'module_general_tab');
	jquery('#paramFile').change(function(){
		loadModParamForm.submit();
	});
	jquery('.entry-edit-head').click(function(){
		jquery(this).next('.fieldset').toggle(400);
	});
});

function ucwords(str) {
	return (str + '').replace(/^([a-z])|[\s_]+([a-z])/g, function ($1) {
		return $1.toUpperCase();
	})
}

function ucfirst (str) {
    var f = str.charAt(0).toUpperCase();
    return f + str.substr(1);
}
var moduleFactory={
	tabs:null,
	loadParamForm:null,
	form:null,
	init:function(){
		this.tabs=new varienTabs('module_info_tabs', 'module_create_form', 'module_general_tab');
		this.loadParamForm=new varienForm('mod_load_param_form');
		this.form=new varienForm('module_create_form');
		
		jquery('#paramFile').change(function(){
			moduleFactory.loadParamForm.submit();
		});
		jquery('.entry-edit-head').click(function(){
			jquery(this).next('.fieldset').toggle(400);
		});
		
		jquery('.validate-strict').change(function(){
			jquery(this).val(jquery(this).val().replace(/[^a-zA-Z0-9]/g, ''));
		});
		jquery('.ucfirst').change(function(){
			jquery(this).val(ucfirst(jquery(this).val()));
		});
		jquery('.ucwords').change(function(){
			jquery(this).val(ucwords(jquery(this).val()));
		});
		
		jquery('#ControllersPopupType').change(function(){
			if(jquery(this).val()=='frontend'){
				jquery('#ControllersPopupExtends option[controllerType=frontend]').removeAttr('disabled');
				jquery('#ControllersPopupExtends option[controllerType=admin]').attr('disabled', 'disabled');
				if(jquery('#ControllersPopupExtends option:selected').attr('controllerType')=='admin'){
					jquery('#ControllersPopupExtends').val('Mage_Core_Controller_Front_Action');
				}
			} else {
				jquery('#ControllersPopupExtends option[controllerType=admin]').removeAttr('disabled');
				jquery('#ControllersPopupExtends option[controllerType=frontend]').attr('disabled', 'disabled');
				if(jquery('#ControllersPopupExtends option:selected').attr('controllerType')=='frontend'){
					jquery('#ControllersPopupExtends').val('Mage_Adminhtml_Controller_Action');
				}
			}
		});
	},
	prepareForm:function(){
		this.components.prepareForm();
		this.controllers.prepareForm();
		this.systemConfig.prepareForm();
	},
	submit:function(){
		this.prepareForm();
		this.form.submit();
	},
	general:{
		/* ---------- attr ---------- */
		/* ---------- methods ---------- */
		getModuleName:function(){},
		getPackageName:function(){},
		getVersion:function(){},
		setModuleName:function(){},
		setPackageName:function(){},
		setVersion:function(){}
		/* ---------- classes ---------- */
	},
	components:{
		/* ---------- attr ---------- */
		treeSelector:'#ComponentsTree',
		items:{'model':[], 'block':[], 'helper':[]},
		defaultExtends:{'model':'', 'block':'', 'helper':''},
		extendsOptions:{'model':'', 'block':'', 'helper':''},
		/* ---------- methods ---------- */
		prepareForm:function(){
			jquery('#ComponentsInputs').html('');
			var types = ['model', 'block', 'helper'];
			for(var i in types){
				var type=types[i];
				var components=moduleFactory.components.items[type];
				for(var i2 in components){
					if(components[i2] && components[i2].node){
						jquery('#ComponentsInputs').append('<input type="hidden" name="'+type+'[]" value="'+components[i2].getFormValue()+'" />');
						jquery('#ComponentsInputs').append('<input type="hidden" name="'+type+'Extends[]" value="'+components[i2].getExtends()+'" />');
						jquery('#ComponentsInputs').append('<input type="hidden" name="'+type+'Override[]" value="'+components[i2].getOverride()+'" />');
					}
				}
			}
		},
		addNew:function(node){
			var type=String(node.attr('componentType')).toLowerCase();
			var newItem=new moduleFactory.components.Item(node, type);
			if(newItem.node && newItem.node.attr('id')!='ComponentsTree'){
				moduleFactory.components.items[type].push(newItem);
			}
		},
		remove:function(node){
			var type = String(node.attr('componentType')).toLowerCase();
			var key = node.attr('key');
			jquery(moduleFactory.components.treeSelector).jstree('delete_node', node);
			delete moduleFactory.components.items[type][key];
		},
		
		/* ---------- classes ---------- */
		Item:function(parent, type){
			/* ---------- attr ---------- */
			var components=moduleFactory.components.items[type];
			this.type=String(type).toLowerCase();
			this.key=components.length;
			this.parent=parent;
			/* ---------- methods ---------- */
			this.getFormValue=function(){
				var path =jquery(moduleFactory.components.treeSelector).jstree('get_path', this.node, false);
				path.splice(0, 1);
				path=path.join('_');
				path=path.substr(0, path.length-4);//remove '.php'
				return path;
			}
			this.getName=function(){
				return jquery(moduleFactory.components.treeSelector).jstree('get_text', this.node);
			};
			this.getExtends=function(){
				return this.node.attr('extends');
			};
			this.setExtends=function(extend){
				this.node.attr('extends', extend);
			};
			this.getOverride=function(){
				return this.node.attr('override');
			};
			this.setOverride=function(override){
				this.node.attr('override', override);
			};
			this.openPopup=function(){
				jquery('#ComponentsPopup').dialog('option', 'title', 'Parameters : '+jquery(moduleFactory.components.treeSelector).jstree('get_path', this.node, false).join('/'));
				jquery('#ComponentsPopupExtends').html(moduleFactory.components.extendsOptions[this.type]);
				jquery('#ComponentsPopupExtends').val(this.getExtends());
				jquery('#ComponentsPopupOverride').val(this.getOverride());
				jquery('#ComponentsPopup').dialog('open');
				jquery('#ComponentsPopup').next('.ui-dialog-buttonpane').find('.ui-button').unbind('click');
				var comp=this;
				jquery('#ComponentsPopup').next('.ui-dialog-buttonpane').find('.ui-button').click(function(){
					comp.closePopup();
				});
				jquery('#ComponentsPopup').prev('.ui-dialog-titlebar').find('.ui-dialog-titlebar-close').remove();
			};
			this.closePopup=function(){
				this.setExtends(jquery('#ComponentsPopupExtends').val());
				this.setOverride(jquery('#ComponentsPopupOverride').val());
				jquery('#ComponentsPopup').dialog('close');
			};
			/* ---------- init script ---------- */
			this.node=jquery(moduleFactory.components.treeSelector).jstree('create_node', parent, 'inside', {
				'attr':{'rel':'component','componentType':this.type,'key':this.key,'extends':moduleFactory.components.defaultExtends[this.type],'override':0
				}, 'data':'New'+ucfirst(this.type)+'.php'}, function(child){
					this.open_node(parent);
					this.rename(child);
			});
		}
	},
	routers:{
		/* ---------- attr ---------- */
		frontendSelector:'#frontendRouter',
		adminSelector:'#adminRouter',
		/* ---------- methods ---------- */
		setFrontend:function(val){
			jquery(moduleFactory.routers.frontendSelector).val(val);
		},
		setAdmin:function(val){
			jquery(moduleFactory.routers.adminSelector).val(val);
		},
		getFrontend:function(){
			return jquery(moduleFactory.routers.frontendSelector).val();
		},
		getAdmin:function(){
			return jquery(moduleFactory.routers.adminSelector).val();
		}
	},
	controllers:{
		/* ---------- attr ---------- */
		treeSelector:'#ComponentsTree',
		defaultExtends:'',
		extendsOptions:'',
		items:[],
		/* ---------- methods ---------- */
		prepareForm:function(){
			jquery('#ControllersInputs').html('');
			var components=moduleFactory.controllers.items;
			var hasType={'admin':false,'frontend':false};
			for(var i2 in components){
				if(components[i2] && components[i2].node){
					var type=components[i2].getType();
					hasType[type]=true;
					jquery('#ControllersInputs').append('<input type="hidden" name="'+type+'Controller[]" value="'+components[i2].getFormValue()+'" />');
					jquery('#ControllersInputs').append('<input type="hidden" name="'+type+'ControllerExtends[]" value="'+components[i2].getExtends()+'" />');
					jquery('#ControllersInputs').append('<input type="hidden" name="'+type+'ControllerOverride[]" value="'+components[i2].getOverride()+'" />');
				}
			}
			if(hasType['admin']){
				jquery('#adminRouter').addClass('required-entry');
			} else {
				jquery('#adminRouter').removeClass('required-entry')
			}
			if(hasType['frontend']){
				jquery('#frontendRouter').addClass('required-entry');
			} else {
				jquery('#frontendRouter').removeClass('required-entry')
			}
		},
		addNew:function(node){
			var newItem=new moduleFactory.controllers.Item(node);
			if(newItem.node && newItem.node.attr('id')!='ComponentsTree'){
				moduleFactory.controllers.items.push(newItem);
			}
		},
		remove:function(node){
			var key = node.attr('key');
			jquery(moduleFactory.controllers.treeSelector).jstree('delete_node', node);
			delete moduleFactory.controllers.items[key];
		},
		/* ---------- classes ---------- */
		Item:function(parent){
			/* ---------- attr ---------- */
			this.key=moduleFactory.controllers.items.length;
			this.parent=parent;
			/* ---------- methods ---------- */
			this.getFormValue=function(){
				var path=jquery(moduleFactory.controllers.treeSelector).jstree('get_path', this.node, false);
				path.splice(0,1);
				path=path.join('_');
				path=path.substr(0, (path.length-14));//String('controller.php').length==14
				return path;
			};
			this.getUrl=function(){
				var path=jquery(moduleFactory.controllers.treeSelector).jstree('get_path', this.node, false);
				path.splice(0,1);
				path=path.join('_');
				path=path.toLowerCase();
				return '/router/'+path.substr(0, (path.length-14))+'/action';//String('controller.php').length==14
			};
			this.getType=function(){
				return this.node.attr('controllerType');
			};
			this.setType=function(controllerType){
				this.node.attr('controllerType', controllerType);
			};
			this.getExtends=function(){
				return this.node.attr('extends');
			};
			this.setExtends=function(extend){
				this.node.attr('extends', extend);
			};
			this.getOverride=function(){
				return this.node.attr('override');
			};
			this.setOverride=function(override){
				this.node.attr('override', override);
			};
			this.openPopup=function(){
				jquery('#ControllersPopup').dialog('option', 'title', 'Parameters : '+this.getUrl());
				jquery('#ControllersPopupExtends').html(moduleFactory.controllers.extendsOptions);
				jquery('#ControllersPopupType').val(this.getType());
				jquery('#ControllersPopupExtends').val(this.getExtends());
				jquery('#ControllersPopupType').change();
				var override = this.getOverride().split('/');
				jquery('#ControllersPopupOverride0').val(override[0]);
				jquery('#ControllersPopupOverride1').val(override[1]);
				jquery('#ControllersPopupOverride2').val(override[2]);
				jquery('#ControllersPopup').dialog('open');
				jquery('#ControllersPopup').next('.ui-dialog-buttonpane').find('.ui-button').unbind('click');
				var ctrl=this;
				jquery('#ControllersPopup').next('.ui-dialog-buttonpane').find('.ui-button').click(function(){
					ctrl.closePopup();
				});
				jquery('#ControllersPopup').prev('.ui-dialog-titlebar').find('.ui-dialog-titlebar-close').remove();
			}
			this.closePopup=function(){
				this.setType(jquery('#ControllersPopupType').val());
				this.setExtends(jquery('#ControllersPopupExtends').val());
				this.setOverride(jquery('#ControllersPopupOverride0').val()+'/'+jquery('#ControllersPopupOverride1').val()+'/'+jquery('#ControllersPopupOverride2').val());
				jquery('#ControllersPopup').dialog('close');
			};
			/* ---------- init script ---------- */
			this.node=jquery(moduleFactory.controllers.treeSelector).jstree('create_node', parent, 'inside', {
				'attr':{'rel':'controller','key':this.key,'extends':moduleFactory.controllers.defaultExtends,'override':'','controllerType':'frontend'
				}, 'data':'NewController.php'}, function(child){
					this.open_node(parent);
					this.rename(child);
			});
		}
	},
	SQLscripts:{
		/* ---------- attr ---------- */
		installSelector:'',
		upgradesSelector:'#newSqlUpgrade',
		upgradeTemplate:'',
		upgrades:[],
		/* ---------- methods ---------- */
		getInstall:function(){
			return jquery(moduleFactory.SQLscripts.installSelector).val();
		},
		setInstall:function(val){
			jquery(moduleFactory.SQLscripts.installSelector).val(val);
		},
		addNewUpgrade:function(){
			moduleFactory.SQLscripts.upgrades.push(new moduleFactory.SQLscripts.Upgrade());
		},
		removeUpgrade:function(key){
			jquery(moduleFactory.SQLscripts.upgradesSelector+key).remove();
			delete moduleFactory.SQLscripts.upgrades[key];
		},
		/* ---------- classes ---------- */
		Upgrade:function(){
			/* ---------- attr ---------- */
			this.key=moduleFactory.SQLscripts.upgrades.length;
			/* ---------- init script ---------- */
			var row2add = moduleFactory.SQLscripts.upgradeTemplate.replace(/{{id}}/g, this.key).replace(/{{from}}/g, '').replace(/{{to}}/g, '');
			jquery(moduleFactory.SQLscripts.upgradesSelector).append(row2add);
		}
	},
	systemConfig:{
		treeSelector:'#SystemConfigTree',
		fieldSelector:'#system_config_xml_tree',
		tabs:[],
		sections:[],
		groups:[],
		fields:[],
		prepareForm:function(){
			if(jquery(moduleFactory.systemConfig.treeSelector).find('li[user_created=1]').length>0){
				jquery(moduleFactory.systemConfig.fieldSelector).val(jquery(moduleFactory.systemConfig.treeSelector).jstree('get_xml', 'nest', -1, ['id', 'user_created', 'fieldType']));
			} else {
				jquery(moduleFactory.systemConfig.fieldSelector).val('');
			}
		},
		addNew:function(parent){
			
		},
		remove:function(node){
			var key = node.attr('key');
			var type= node.attr('rel')+'s';
			jquery(moduleFactory.systemConfig.treeSelector).jstree('delete_node', node);
			delete moduleFactory.systemConfig[type][key];
		},
		Items:function(parent){
			
		}
	}
}
jquery(document).ready(function(){
	moduleFactory.init();
});
var keepAliveUrl;
function keepAlive(){
	jquery.post(keepAliveUrl, {form_key:FORM_KEY}, function(){
		setTimeout(keepAlive, 300000);//5 minutes
	}, null);
}
setTimeout(keepAlive, 300000);//5 minutes