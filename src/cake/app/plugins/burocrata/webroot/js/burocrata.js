const E_NOT_JSON = 1; // Not a JSON response
const E_JSON = 2; // JSON tells me the error

/**
 * Abstract class that implements the behaviour of callbacks
 * with the methods `addCallback` and `trigger`
 */
var Callbackable = Class.create({
	addCallbacks: function(callbacks)
	{
		if(typeof(this.callbacks) == 'undefined')
			this.callbacks = $H({});
		
		this.callbacks = this.callbacks.merge(callbacks); 
		return this;
	},
	trigger: function()
	{
		if(typeof(this.callbacks) == 'undefined')
			this.callbacks = $H({});
		
		var callback = arguments[0];
		var callback_function = this.callbacks.get(callback);
		if(!callback_function)
			return false;
		
		var i,args = new Array();
		for(i = 1; i < arguments.length; i++)
			args.push(arguments[i]);
		
		callback_function.apply(this, args);
		
		return true;
	}
});


/**
 * Adds all javascript functionality to the form.
 *
 * Callbacks:
 * - `onStart` function (form){}
 * - `onComplete` function (form, response){}
 * - `onFailure` function (form, response){}
 * - `onError` function (error){}
 * - `onSave` function (form, response, json, saved){}
 * - `onReject` function (form, response, json, saved){}
 * - `onSuccess` function (form, response, json){}
 *
 */
var BuroForm = Class.create(Callbackable, {
	initialize: function()
	{
		this.url = arguments[0];

		this.form = $(arguments[1]);
		this.form.reset = this.resetForm.bind(this);
		this.form.lock = this.lockForm.bind(this);
		this.form.unlock = this.unlockForm.bind(this);
		
		this.form.observe('keypress', function(ev){
			var element = ev.findElement().nodeName.toLowerCase();
			var key = ev.keyCode;
			if(key == 13 && element == 'input')
				this.submits();
		}.bind(this));

		this.submit = $(arguments[2]);
		this.submit.observe('click', this.submits.bind(this));
		
		this.inputs = Form.getElements(this.form);
	},
	lockForm: function()
	{
		this.form.setOpacity(0.5);
		this.inputs.each(Form.Element.disable);
	},
	unlockForm: function()
	{
		this.form.setOpacity(1);
		this.inputs.each(Form.Element.enable);
	},
	resetForm: function()
	{
		
	},
	submits: function(ev)
	{
		var data = Form.serializeElements(this.inputs);
		this.trigger('onStart', this.form);
		new Ajax.Request(this.url, {
			parameters: data,
			onComplete: function (response) { this.trigger('onComplete', this.form, response); }.bind(this),
			onFailure: function (response) { this.trigger('onFailure', this.form, response); }.bind(this),
			onSuccess: function (response) {
				if(response.responseJSON) this.json = response.responseJSON;
				
				if(this.json && this.json.error != false)
					this.trigger('onError', this.json.error);
				
				if(this.json && this.json.saved === true)	
					this.trigger('onSave',this.form, response, this.json, this.json.saved);
				else if(this.json && this.json.saved === false)
					this.trigger('onReject', this.form, response, this.json, this.json.saved);
				
				this.trigger('onSuccess', this.form, response, this.json);
			}.bind(this)
		});
	}
});

// Callbacks graph:
//
//         start
//       ____|___
//     |http ok?|
//         /  \
//        /    \
// failure    success
//     |    ____|______
//     |   |data saved?|
//     |        /\
//     \    save  reject
//      \     |     /
//       \    |    /
//         complete

/**
 * Extends the default Autocomplete built in Scriptaculous.
 *
 * Callbacks:
 * - `onStart` function (input){}
 * - `onComplete` function (input, response){}
 * - `onFailure` function (input, response){}
 * - `onError` function (code, error){}
 * - `onSuccess` function (input, response, json){}
 *
 */
var BuroAutocomplete = Class.create(Callbackable, {
	initialize: function(url, id_base, options)
	{
		var id_of_text_field = 'input'+id_base,
			id_of_div_to_populate = 'div'+id_base;
		options.updateElement = this.alternateUpdateElement.bind(this);
		
		this.autocompleter = new Ajax.Autocompleter(id_of_text_field, id_of_div_to_populate, url, options);
		this.autocompleter.options.onComplete = this.onComplete.bind(this);
		this.autocompleter.options.onFailure = this.onFailure.bind(this);
		this.autocompleter.options.onSuccess = this.onSuccess.bind(this);
		
		this.input = this.autocompleter.element;
		
		this.addCallbacks({
			onSelect: function(pair, li_item){
				alert(pair.key);
				alert(pair.value);
			}
		});
	},
	
	onSuccess: function(response)
	{
		if(response && response.responseJSON)
			this.trigger('onSuccess', this.input, response, response.responseJSON);
	},
	
	onFailure: function(response)
	{
		this.trigger('onFailure', this.input, response);
	},
	
	onComplete: function(response)
	{
		if(!response || !response.responseJSON) {
			this.trigger('onError', E_NOT_JSON);
			return;
		}
		
		this.json = response.responseJSON;
		if(this.json.error != false)
		{
			this.trigger('onError', E_JSON, this.json.error);
			return;
		}
		if (Object.isArray(this.json.content))
			this.json.content = {};
		this.foundContent = $H(this.json.content);
		this.autocompleter.updateChoices(this.createChoices());
		
		this.trigger('onComplete', this.input, response);
	},
	
	getUpdatedChoices: function($super)
	{
		this.trigger('onStart', this.input);
		$super();
	},
	
	createChoices: function()
	{
		var i, ul = new Element('ul');
		var keys = this.foundContent.keys();
		for(i = 0; i < keys.length; i++)
			ul.insert(new Element('li').update(this.foundContent.get(keys[i])));
		
		return ul;
	},
	
	alternateUpdateElement: function(selectedElement)
	{
		var pair = {id: this.foundContent.keys()[this.autocompleter.index]};
		pair.value = this.foundContent.get(pair.id);
		
		this.trigger('onSelect', pair, selectedElement, this.input);
	}
});