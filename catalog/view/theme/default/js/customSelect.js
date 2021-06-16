var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Select = function () {
	function Select(options) {
		_classCallCheck(this, Select);
		
		var defaultOptions = {
			selector: 'select',
			customSelectClassName: 'select',
			customSelectActiveClassName: 'select--open',
			currentClassName: 'select__current',
			selectListClassName: 'select__list',
			selectItemClassName: 'select__item',
			activeItemClass: 'select__item--active',
			disableItemClass: 'select__item--disabled',
			activeClass: 'select--open',
			event: 'click',
			onChange: function onChange() /* select */{
				// select onChange event
			}
		};
		this.options = _extends({}, defaultOptions, options);
		
		return this.init(this.options.selector);
	}
	
	_createClass(Select, [{
		key: 'init',
		value: function init(selector) {
			var _this = this;
			
			var selects = document.querySelectorAll(selector);
			if (!selects) return;
			selects.forEach(function (element) {
				var customSelect = _this.renderSelect(element);
				element.insertAdjacentElement('afterEnd', customSelect);
			});
		}
		}, {
		key: 'update',
		value: function update() {
			var oldSelect = document.querySelectorAll('.select');
			oldSelect.forEach(function (e) {
				e.parentNode.removeChild(e);
			});
			this.init(this.options.selector);
		}
		}, {
		key: 'renderSelect',
		value: function renderSelect(select) {
			var _this2 = this;
			
			var currentElement = document.createElement('span');
			var customSelectList = document.createElement('ul');
			var customSelect = document.createElement('div');
			var nativeSelectClasses = select.className.split(' ');
			
			// add classes to custm select
			customSelect.classList.add(this.options.customSelectClassName);
			if (select.className) {
				var _customSelect$classLi;
				
				(_customSelect$classLi = customSelect.classList).add.apply(_customSelect$classLi, _toConsumableArray(nativeSelectClasses));
			}
			
			// add tabindex if it exist
			if (select.getAttribute('tabindex')) {
				customSelect.setAttribute('tabindex', select.getAttribute('tabindex'));
			}
			
			// add disabled class if it exist
			if (select.disabled) {
				customSelect.classList.add('disabled');
			}
			currentElement.classList.add(this.options.currentClassName);
			customSelectList.classList.add(this.options.selectListClassName);
			
			customSelect.appendChild(currentElement);
			customSelect.appendChild(customSelectList);
			
			var options = select.querySelectorAll('option');
			if (!options) return;
			var selected = select.querySelector('option:checked') || select.querySelector('option:first-child');
			
			// set current
			if (!selected) return;
			var currentText = selected.getAttribute('data-display') || selected.innerText;
			currentElement.innerText = currentText;
			
			// build list
			options.forEach(function (option) {
				var display = option.getAttribute('data-display');
				var nativeOptionClasses = option.className.split(' ');
				var item = document.createElement('li');
				item.classList.add(_this2.options.selectItemClassName);
				
				if (option.className) {
					var _item$classList;
					
					(_item$classList = item.classList).add.apply(_item$classList, _toConsumableArray(nativeOptionClasses));
				}
				
				if (option.selected) {
					item.classList.add(_this2.options.activeItemClass);
				}
				
				if (option.disabled) {
					item.classList.add(_this2.options.disableItemClass);
				}
				
				item.setAttribute('data-value', option.value);
				item.innerText = display || option.innerText;
				customSelectList.appendChild(item);
			});
			this.addListeners(select, customSelect);
			
			return customSelect;
		}
		}, {
		key: 'addListeners',
		value: function addListeners(select, customSelect) {
			var options = this.options;
			
			
			select.addEventListener('change', function selectChangeFn() {
				if (typeof options.onChange === "function") {
					options.onChange(this);
				}
			});
			
			customSelect.addEventListener('click', function selectToggleClass(event) {
				this.classList.toggle(options.customSelectActiveClassName);
				document.body.classList.toggle('select-is-open');
				event.stopPropagation();
			});
			
			document.body.addEventListener('click', function () {
				var openSelect = document.querySelectorAll('.select');
				for (var i = 0; i < openSelect.length; i += 1) {
					openSelect[i].classList.remove(options.customSelectActiveClassName);
				}
			});
			
			var optionsList = customSelect.getElementsByClassName(options.selectItemClassName);
			var currentElement = customSelect.getElementsByClassName(options.currentClassName)[0];
			var naviveOptions = select.querySelectorAll('option');
			
			Array.prototype.forEach.call(optionsList, function (item) {
				item.addEventListener('click', function addActiveClassName(event) {
					if (this.classList.contains(options.activeItemClass)) {
						return;
					}
					
					if (this.classList.contains(options.disableItemClass)) {
						event.stopPropagation();
						return;
					}
					var index = Array.prototype.indexOf.call(this.parentElement.children, this);
					
					Array.prototype.forEach.call(customSelect.getElementsByClassName(options.selectItemClassName), function (element) {
						element.classList.remove(options.activeItemClass);
					});
					
					currentElement.innerText = this.innerText;
					this.classList.add(options.activeItemClass);
					
					// change select value
					select.value = this.getAttribute('data-value');
					naviveOptions.forEach(function (nativeItem) {
						nativeItem.selected = false;
					});
					naviveOptions[index].selected = true;
					var changeEvent = document.createEvent('Event');
					changeEvent.initEvent('change', true, true);
					select.dispatchEvent(changeEvent);
				});
			});
		}
	}]);
	
	return Select;
}();