// document.addEventListener('DOMContentLoaded', () => {
// 	const checkLabels = document.querySelectorAll('.check-label')

// 	if (checkLabels.length != 0) {
// 		checkLabels.forEach(label => {
// 			label.addEventListener('click', function (e) {
// 				if (e.target.tagName.toLowerCase() === 'input') return

// 				const checkbox = label.querySelector('input[type="checkbox"]')
// 				const checkboxItem = label.querySelector('.checkbox-item')
// 				checkbox.checked = !checkbox.checked
// 				if (checkbox.checked) {
// 					checkboxItem.classList.add('active')
// 				} else {
// 					checkboxItem.classList.remove('active')
// 				}
// 				e.preventDefault()
// 			})

// 			// При изменении состояния чекбокса (например, с клавиатуры)
// 			const checkbox = label.querySelector('input[type="checkbox"]')
// 			const checkboxItem = label.querySelector('.checkbox-item')
// 			checkbox.addEventListener('change', function () {
// 				if (checkbox.checked) {
// 					checkboxItem.classList.add('active')
// 				} else {
// 					checkboxItem.classList.remove('active')
// 				}
// 			})
// 		})
// 	}

// 	// валидация формы
// 	const form = document.querySelector('.verification-form__form form')
// 	if (form) {
// 		form.addEventListener('submit', function (e) {
// 			let valid = true

// 			form.querySelectorAll('.js-error').forEach(el => el.remove())
// 			form.querySelectorAll('.error').forEach(el => el.classList.remove('error'))

// 			const requiredFields = [{ name: 'user-name' }, { name: 'user-street' }, { name: 'user-city' }, { name: 'user-state' }, { name: 'user-zip' }, { name: 'user-country' }]
// 			requiredFields.forEach(field => {
// 				const input = form.querySelector(`[name="${field.name}"]`)
// 				if (input && !input.value.trim()) {
// 					input.classList.add('error')
// 					valid = false
// 				}
// 			})

// 			const dob = form.querySelector('input[type="date"]')
// 			if (dob && !dob.value) {
// 				dob.classList.add('error')
// 				valid = false
// 			}

// 			if (!valid) {
// 				e.preventDefault()
// 				form.scrollIntoView({ behavior: 'smooth', block: 'start' })
// 			}
// 		})
// 	}

// 	const phoneInput = document.querySelector('#phone')
// 	if (phoneInput && window.intlTelInput) {
// 		const iti = window.intlTelInput(phoneInput, {
// 			initialCountry: 'us',
// 			preferredCountries: ['us', 'ru', 'ua', 'kz'],
// 			utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js',
// 		})

// 		function updatePlaceholder() {
// 			const countryData = iti.getSelectedCountryData()
// 			const dialCode = countryData.dialCode ? `+${countryData.dialCode}` : ''
// 			phoneInput.placeholder = dialCode
// 		}

// 		updatePlaceholder()

// 		phoneInput.addEventListener('countrychange', updatePlaceholder)
// 	}

// 	const faqItem = document.querySelectorAll('.faq__item button')

// 	if (faqItem.length != 0) {
// 		faqItem.forEach(item => {
// 			item.addEventListener('click', e => {
// 				let parentItem = e.target.closest('.faq__item')

// 				if (parentItem.classList.contains('active')) {
// 					CloseFaq(parentItem)
// 				} else {
// 					OpenFaq(parentItem)
// 				}
// 			})
// 		})

// 		function CloseFaq(item = null) {
// 			faqItem.forEach(item => {
// 				item.closest('.faq__item').classList.remove('active')
// 			})
// 		}
// 		function OpenFaq(item) {
// 			CloseFaq()
// 			item.classList.add('active')
// 		}
// 	}

// 	document.getElementById('messageTextarea').addEventListener('input', function () {
// 		const counter = this.closest('.formConnect__companyName-element').querySelector('.current')
// 		counter.textContent = this.value.length
// 	})
// 	document.getElementById('donate	Textarea').addEventListener('input', function () {
// 		const counter = this.closest('.formConnect__companyName-element').querySelector('.current')
// 		counter.textContent = this.value.length
// 	})
// });
// document.addEventListener('DOMContentLoaded', () => {
// 	// Инициализация Swiper слайдера
// 	const swiper = new Swiper('.creatorPage__content-products-cards', {
// 		slidesPerView: 'auto',
// 		spaceBetween: 20,
// 		navigation: {
// 			nextEl: '.arrow-right',
// 			prevEl: '.arrow-left',
// 		},
// 		breakpoints: {
// 			320: {
// 				slidesPerView: 1,
// 				spaceBetween: 10,
// 			},
// 			768: {
// 				slidesPerView: 2,
// 				spaceBetween: 15,
// 			},
// 			1024: {
// 				slidesPerView: 3,
// 				spaceBetween: 20,
// 			},
// 		},
// 	})

// 	// Функционал лайков (сердечек)
// 	document.querySelectorAll('.heart').forEach(heart => {
// 		heart.addEventListener('click', function () {
// 			const path = this.querySelector('path')
// 			if (path) {
// 				path.getAttribute('fill') === '#FF2C0C' ? path.removeAttribute('fill') : path.setAttribute('fill', '#FF2C0C')
// 			}
// 		})
// 	})
// 	const headerFilters = document.querySelector('.filters__title-container')
// 	const headerFiltersIcon = headerFilters ? headerFilters.querySelector('svg') : null
// 	const filtersList = document.querySelector('.filters__list')
// 	const filtersItems = document.querySelectorAll('.filers__item')
// 	const platforms = document.querySelectorAll('.filters__platforms-item')
// 	const languages = document.querySelectorAll('.filter__item-langauge')
// 	const rangeMin = document.querySelector('.range-min')
// 	const rangeMax = document.querySelector('.range-max')
// 	const minValue = document.querySelector('.min-value')
// 	const maxValue = document.querySelector('.max-value')
// 	const track = document.querySelector('.slider-track')
// 	const maxTotal = 9999999
// 	const hearts = document.querySelectorAll('.heart')

// 	if (headerFilters && headerFiltersIcon && filtersList) {
// 		headerFilters.addEventListener('click', () => {
// 			filtersList.classList.toggle('hidden-filters')
// 			headerFiltersIcon.style.transform = headerFiltersIcon.style.transform === 'rotate(180deg)' ? 'rotate(0deg)' : 'rotate(180deg)'
// 		})
// 	}

// 	hearts.forEach(heart => {
// 		heart.addEventListener('click', function () {
// 			const path = this.querySelector('path')

// 			if (path.getAttribute('fill') === '#FF2C0C') {
// 				path.removeAttribute('fill')
// 			} else {
// 				path.setAttribute('fill', '#FF2C0C')
// 			}
// 		})
// 	})

// 	platforms.forEach(item => {
// 		item.addEventListener('click', () => {
// 			item.classList.toggle('active-filters')
// 		})
// 	})

// 	languages.forEach(item => {
// 		item.addEventListener('click', () => {
// 			item.classList.toggle('active-filters')
// 		})
// 	})

// 	filtersItems.forEach(item => {
// 		const title = item.querySelector('.filters__item-title')
// 		const filter = item.querySelector('.filter')

// 		if (title && filter) {
// 			title.addEventListener('click', () => {
// 				filter.classList.toggle('unactive-fiter')
// 				title.classList.toggle('show-after')
// 			})
// 		}
// 	})

// 	function formatNumber(num) {
// 		return new Intl.NumberFormat('ru-RU').format(num)
// 	}

// 	function updateSlider() {
// 		const min = parseInt(rangeMin.value)
// 		const max = parseInt(rangeMax.value)

// 		minValue.textContent = formatNumber(min)
// 		maxValue.textContent = formatNumber(max)

// 		const minPercent = (min / maxTotal) * 100
// 		const maxPercent = (max / maxTotal) * 100

// 		track.style.background = `
//             linear-gradient(to right,
//             #e1e1e1 0%,
//             #e1e1e1 ${minPercent}%,
//             #FC7361 ${minPercent}%,
//             #FC7361 ${maxPercent}%,
//             #e1e1e1 ${maxPercent}%,
//             #e1e1e1 100%)
//         `
// 	}

// 	if (rangeMin && rangeMax && minValue && maxValue && track) {
// 		updateSlider()

// 		rangeMin.addEventListener('input', function () {
// 			if (parseInt(this.value) > parseInt(rangeMax.value)) {
// 				this.value = rangeMax.value
// 			}
// 			updateSlider()
// 		})

// 		rangeMax.addEventListener('input', function () {
// 			if (parseInt(this.value) < parseInt(rangeMin.value)) {
// 				this.value = rangeMin.value
// 			}
// 			updateSlider()
// 		})
// 	}

// 	document.querySelector('.popUp__edit-contact').showModal()
// 	// Обработка выбора файла
// 	document.querySelector('.modern-upload input').addEventListener('change', function (e) {
// 		const file = e.target.files[0]
// 		const fileInfo = document.querySelector('.file-info')
// 		const uploadText = document.querySelector('.upload-text')

// 		if (file) {
// 			// Проверка типа файла
// 			if (!file.type.match('image.*')) {
// 				fileInfo.textContent = 'Пожалуйста, выберите изображение'
// 				fileInfo.style.color = 'red'
// 				return
// 			}

// 			// Проверка размера изображения
// 			const img = new Image()
// 			img.onload = function () {
// 				if (this.width !== 350 || this.height !== 100) {
// 					fileInfo.textContent = `Размер: ${this.width}x${this.height}px (требуется 350x100)`
// 					fileInfo.style.color = 'orange'
// 				} else {
// 					fileInfo.textContent = file.name
// 					fileInfo.style.color = 'green'
// 					uploadText.textContent = 'Файл загружен'
// 				}
// 			}
// 			img.src = URL.createObjectURL(file)
// 		}
// 	})
// });
// document.addEventListener('DOMContentLoaded', function () {
// 	// Инициализация первого редактора (tiptap-editor)
// 	const editor1 = document.getElementById('tiptap-editor')
// 	const hiddenTextarea1 = document.getElementById('Article-Content')
// 	initEditor(editor1, hiddenTextarea1, {
// 		boldBtn: 'bold-btn',
// 		alignLeftBtn: 'align-left-btn',
// 		alignCenterBtn: 'align-center-btn',
// 		alignRightBtn: 'align-right-btn',
// 		alignJustifyBtn: 'align-justify-btn',
// 		listUlBtn: 'list-ul-btn',
// 		listOlBtn: 'list-ol-btn',
// 		indentBtn: 'indent-btn',
// 	})

// 	// Инициализация второго редактора (tiptap-editor-desc)
// 	const editor2 = document.getElementById('tiptap-editor-desc')
// 	const hiddenTextarea2 = document.getElementById('product-description')
// 	initEditor(editor2, hiddenTextarea2, {
// 		boldBtn: 'desc-bold-btn',
// 		alignLeftBtn: 'desc-align-left-btn',
// 		alignCenterBtn: 'desc-align-center-btn',
// 		alignRightBtn: 'desc-align-right-btn',
// 		alignJustifyBtn: 'desc-align-justify-btn',
// 		listUlBtn: 'desc-list-ul-btn',
// 		listOlBtn: 'desc-list-ol-btn',
// 		indentBtn: 'desc-indent-btn',
// 	})

// 	// Инициализация третьего редактора (tiptap-editor-desc2)
// 	const editor3 = document.getElementById('tiptap-editor-desc2')
// 	const hiddenTextarea3 = document.getElementById('product-description2')
// 	initEditor(editor3, hiddenTextarea3, {
// 		boldBtn: 'desc2-bold-btn',
// 		alignLeftBtn: 'desc2-align-left-btn',
// 		alignCenterBtn: 'desc2-align-center-btn',
// 		alignRightBtn: 'desc2-align-right-btn',
// 		alignJustifyBtn: 'desc2-align-justify-btn',
// 		listUlBtn: 'desc2-list-ul-btn',
// 		listOlBtn: 'desc2-list-ol-btn',
// 		indentBtn: 'desc2-indent-btn',
// 	})

// 	// Общая функция инициализации редактора
// 	function initEditor(editor, hiddenTextarea, buttons) {
// 		// Инициализация содержимого
// 		hiddenTextarea.value = editor.innerHTML

// 		// Обновление скрытого поля при изменении редактора
// 		editor.addEventListener('input', function () {
// 			hiddenTextarea.value = editor.innerHTML
// 		})

// 		// Функция форматирования текста
// 		function formatText(command, value = null) {
// 			document.execCommand(command, false, value)
// 			editor.focus()
// 			updateButtonStates()
// 		}

// 		// Обновление состояния кнопок
// 		function updateButtonStates() {
// 			const allButtons = [
// 				{ id: buttons.boldBtn, command: 'bold' },
// 				{ id: buttons.alignLeftBtn, command: 'justifyLeft' },
// 				{ id: buttons.alignCenterBtn, command: 'justifyCenter' },
// 				{ id: buttons.alignRightBtn, command: 'justifyRight' },
// 				{ id: buttons.alignJustifyBtn, command: 'justifyFull' },
// 			]

// 			allButtons.forEach(btn => {
// 				const buttonEl = document.getElementById(btn.id)
// 				if (buttonEl) {
// 					buttonEl.classList.toggle('active', document.queryCommandState(btn.command))
// 				}
// 			})
// 		}

// 		// Назначение обработчиков для кнопок
// 		if (buttons.boldBtn) {
// 			document.getElementById(buttons.boldBtn).addEventListener('click', () => formatText('bold'))
// 		}
// 		if (buttons.alignLeftBtn) {
// 			document.getElementById(buttons.alignLeftBtn).addEventListener('click', () => formatText('justifyLeft'))
// 		}
// 		if (buttons.alignCenterBtn) {
// 			document.getElementById(buttons.alignCenterBtn).addEventListener('click', () => formatText('justifyCenter'))
// 		}
// 		if (buttons.alignRightBtn) {
// 			document.getElementById(buttons.alignRightBtn).addEventListener('click', () => formatText('justifyRight'))
// 		}
// 		if (buttons.alignJustifyBtn) {
// 			document.getElementById(buttons.alignJustifyBtn).addEventListener('click', () => formatText('justifyFull'))
// 		}
// 		if (buttons.listUlBtn) {
// 			document.getElementById(buttons.listUlBtn).addEventListener('click', () => formatText('insertUnorderedList'))
// 		}
// 		if (buttons.listOlBtn) {
// 			document.getElementById(buttons.listOlBtn).addEventListener('click', () => formatText('insertOrderedList'))
// 		}
// 		if (buttons.indentBtn) {
// 			document.getElementById(buttons.indentBtn).addEventListener('click', () => formatText('indent'))
// 		}

// 		// Обновление состояния кнопок при взаимодействии
// 		editor.addEventListener('keyup', updateButtonStates)
// 		editor.addEventListener('mouseup', updateButtonStates)

// 		// Инициализация состояния кнопок
// 		updateButtonStates()
// 	}

// 	// Обработчик для загрузки файлов
// 	const fileInput = document.querySelector('.modern-upload input[type="file"]')
// 	const fileInfo = document.querySelector('.file-info')
// 	const imagePreview = document.querySelector('.image-preview')
// 	const uploadIcon = document.querySelector('.upload-icon')
// 	const uploadText = document.querySelector('.upload-text')

// 	// Создаем элемент для превью
// 	const previewImage = document.createElement('img')
// 	imagePreview.appendChild(previewImage)

// 	fileInput.addEventListener('change', function () {
// 		if (this.files && this.files[0]) {
// 			const file = this.files[0]

// 			// Отображаем название файла
// 			uploadText.textContent = file.name

// 			// Проверяем, является ли файл изображением
// 			if (file.type.match('image.*')) {
// 				const reader = new FileReader()

// 				reader.onload = function (e) {
// 					// Скрываем иконку и показываем превью
// 					uploadIcon.style.display = 'none'
// 					previewImage.src = e.target.result
// 					previewImage.style.display = 'block'
// 				}

// 				reader.readAsDataURL(file)
// 			} else {
// 				// Если файл не изображение, показываем только название
// 				uploadIcon.style.display = 'block'
// 				previewImage.style.display = 'none'
// 			}
// 		} else {
// 			// Если файл не выбран, возвращаем исходное состояние
// 			uploadIcon.style.display = 'block'
// 			previewImage.style.display = 'none'
// 			uploadText.textContent = 'Выберите изображение'
// 		}
// 	})

// 	// Обработчики для drag-and-drop
// 	const uploadArea = document.querySelector('.upload-area')

// 	uploadArea.addEventListener('dragover', function (e) {
// 		e.preventDefault()
// 		this.style.borderColor = '#4dabf7'
// 	})

// 	uploadArea.addEventListener('dragleave', function () {
// 		this.style.borderColor = '#dee2e6'
// 	})

// 	uploadArea.addEventListener('drop', function (e) {
// 		e.preventDefault()
// 		this.style.borderColor = '#dee2e6'
// 		fileInput.files = e.dataTransfer.files
// 		fileInput.dispatchEvent(new Event('change'))
// 	})

// 	// Инициализация date picker
// 	const dateInput = document.getElementById('Publish-Date')
// 	dateInput.addEventListener('blur', function () {
// 		if (!this.value) {
// 			this.type = 'text'
// 		}
// 	})

// 	// Обработчик для кнопки сохранения
// 	const saveBtn = document.querySelector('.save-btn')
// 	if (saveBtn) {
// 		saveBtn.addEventListener('click', function () {
// 			alert('Содержимое сохранено:\n' + hiddenTextarea1.value)
// 		})
// 	}
// });
// document.addEventListener('DOMContentLoaded', function () {
// 	// Сохраняем оригинальное содержимое для восстановления
// 	document.querySelectorAll('.image-preview').forEach(preview => {
// 		preview.dataset.original = preview.innerHTML
// 	})

// 	// Обработчики для всех file input
// 	document.querySelectorAll('.modern-upload input[type="file"]').forEach(input => {
// 		input.addEventListener('change', function () {
// 			const file = this.files[0]
// 			const container = this.closest('.modern-upload')
// 			const imagePreview = container.querySelector('.image-preview')
// 			const uploadText = container.querySelector('.upload-text')

// 			if (!file) {
// 				if (imagePreview.dataset.original) {
// 					imagePreview.innerHTML = imagePreview.dataset.original
// 				}
// 				uploadText.textContent = ''
// 				return
// 			}

// 			// Обновляем текст с названием файла
// 			uploadText.textContent = file.name

// 			// Обработка изображений
// 			if (file.type.match('image.*')) {
// 				// Освобождаем предыдущий URL
// 				const oldImg = imagePreview.querySelector('img')
// 				if (oldImg && oldImg.src.startsWith('blob:')) {
// 					URL.revokeObjectURL(oldImg.src)
// 				}

// 				const imageUrl = URL.createObjectURL(file)
// 				const img = document.createElement('img')
// 				img.src = imageUrl

// 				imagePreview.innerHTML = ''
// 				imagePreview.appendChild(img)
// 			} else {
// 				// Для не-изображений восстанавливаем оригинал
// 				if (imagePreview.dataset.original) {
// 					imagePreview.innerHTML = imagePreview.dataset.original
// 				}
// 			}
// 		})
// 	})
// });


// document.addEventListener('DOMContentLoaded', function() {
//   // Сохраняем оригинальное содержимое для восстановления
//   document.querySelectorAll('.image-preview').forEach(preview => {
//     preview.dataset.original = preview.innerHTML;
//   });

//   // Инициализируем контейнеры
//   const containers = {
//     additionalPhotos: document.querySelector('.createProduct__additional-list'),
//     productFiles: document.querySelector('.createProduct__files')
//   };

//   // Оставляем только первый элемент в каждом контейнере
//   Object.values(containers).forEach(container => {
//     const items = container.querySelectorAll('li');
//     for (let i = 1; i < items.length; i++) {
//       items[i].remove();
//     }
//   });

//   // Функция для создания нового элемента
//   function createNewItem(container, isAdditional = true) {
//     const firstItem = container.querySelector('li:first-child');
//     const newItem = firstItem.cloneNode(true);
    
//     // Очищаем поля
//     const input = newItem.querySelector('input[type="file"]');
//     input.value = '';
    
//     const imagePreview = newItem.querySelector('.image-preview');
//     imagePreview.innerHTML = imagePreview.dataset.original;
    
//     const uploadText = newItem.querySelector('.upload-text');
//     uploadText.textContent = '';
    
//     // Добавляем обработчик
//     input.addEventListener('change', handleFileChange);
    
//     // Для Additional Photos добавляем иконку плюса
//     if (!isAdditional) {
//       const plusIcon = newItem.querySelector('.createProduct__file-plus');
//       if (plusIcon) plusIcon.remove();
//     }
    
//     return newItem;
//   }

//   // Обработчик изменения файла
//   function handleFileChange() {
//     const file = this.files[0];
//     const container = this.closest('.modern-upload');
//     const imagePreview = container.querySelector('.image-preview');
//     const uploadText = container.querySelector('.upload-text');
    
//     if (!file) {
//       if (imagePreview.dataset.original) {
//         imagePreview.innerHTML = imagePreview.dataset.original;
//       }
//       uploadText.textContent = '';
//       return;
//     }

//     // Обновляем текст с названием файла
//     uploadText.textContent = file.name;

//     // Обработка изображений
//     if (file.type.match('image.*')) {
//       const oldImg = imagePreview.querySelector('img');
//       if (oldImg && oldImg.src.startsWith('blob:')) {
//         URL.revokeObjectURL(oldImg.src);
//       }

//       const imageUrl = URL.createObjectURL(file);
//       const img = document.createElement('img');
//       img.src = imageUrl;
      
//       imagePreview.innerHTML = '';
//       imagePreview.appendChild(img);
//     } else {
//       if (imagePreview.dataset.original) {
//         imagePreview.innerHTML = imagePreview.dataset.original;
//       }
//     }
    
//     // Проверяем нужно ли добавить новый инпут
//     setTimeout(() => checkAddNewInput(this), 100);
//   }

//   // Проверяем нужно ли добавить новый инпут
//   function checkAddNewInput(currentInput) {
//     const parentContainer = currentInput.closest('ul');
//     const isAdditional = parentContainer.classList.contains('createProduct__additional-list');
    
//     // Считаем сколько инпутов уже заполнено
//     const allInputs = parentContainer.querySelectorAll('input[type="file"]');
//     let filledCount = 0;
    
//     allInputs.forEach(input => {
//       if (input.files.length > 0) filledCount++;
//     });
    
//     // Если все заполнены и меньше 8 - добавляем новый
//     if (filledCount === allInputs.length && allInputs.length < 8) {
//       const newItem = createNewItem(parentContainer, isAdditional);
//       parentContainer.appendChild(newItem);
//     }
//   }

//   // Инициализация обработчиков
//   function initInputHandlers() {
//     document.querySelectorAll('.modern-upload input[type="file"]').forEach(input => {
//       input.addEventListener('change', handleFileChange);
//     });
//   }

//   // Запускаем инициализацию
//   initInputHandlers();
// });

// document.addEventListener('DOMContentLoaded', function() {
//   // Обработчики для видео-ссылок
//   function initVideoLinks() {
//     const videoLists = document.querySelectorAll('.createProduct__videoLink-list');
    
//     videoLists.forEach(list => {
//       // Оставляем только первый элемент
//       const items = list.querySelectorAll('.createProduct__videoLink-item');
//       for (let i = 1; i < items.length; i++) {
//         items[i].remove();
//       }
      
//       // Добавляем обработчик на первый элемент
//       addPlusHandler(list.querySelector('.createProduct__videoLink-plus'));
//     });
//   }
  
//   // Функция для добавления обработчика
//   function addPlusHandler(plusIcon) {
//     plusIcon.addEventListener('click', function(e) {
//       e.stopPropagation();
//       const list = this.closest('.createProduct__videoLink-list');
      
//       // Проверяем количество элементов
//       if (list.querySelectorAll('.createProduct__videoLink-item').length >= 5) {
//         return;
//       }
      
//       // Создаем новый элемент
//       const newItem = document.createElement('li');
//       newItem.className = 'createProduct__videoLink-item';
//       newItem.innerHTML = `
//         <input placeholder="Link" type="text" class="input createProduct__videoLink-input" />
//         <svg class="createProduct__videoLink-plus" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
//           <path d="M11.0008 3.2218C11.0008 9.29693 11.0008 12.703 11.0008 18.7782M3.22266 11C9.29779 11 12.7039 11 18.779 11" stroke="#212121" stroke-width="1.5" stroke-linecap="round" />
//         </svg>
//       `;
      
//       // Добавляем в список
//       list.appendChild(newItem);
      
//       // Добавляем обработчик на новую иконку
//       addPlusHandler(newItem.querySelector('.createProduct__videoLink-plus'));
//     });
//   }
  
//   // Инициализируем видео-ссылки
//   initVideoLinks();
// });


// document.addEventListener('DOMContentLoaded', function () {
// 	// Инициализация всех чипс-инпутов с ограничениями
// 	const chipsConfig = [
// 		{
// 			inputId: 'tags-input',
// 			dropdownId: 'product-type-dropdown',
// 			tagsListId: 'product-type-tags',
// 			chipClass: 'createArticle__tags-item',
// 			maxTags: null, // Без ограничений
// 		},
// 		{
// 			inputId: 'location-input',
// 			dropdownId: 'location-dropdown',
// 			tagsListId: 'location-tags',
// 			chipClass: 'createProduct__location-tag createArticle__tags-item',
// 			maxTags: 3, // Максимум 3 тега
// 		},
// 		{
// 			inputId: 'categories-input',
// 			dropdownId: 'categories-dropdown',
// 			tagsListId: 'categories-tags',
// 			chipClass: 'createProduct__location-tag createArticle__tags-item',
// 			maxTags: 5, // Максимум 5 тегов
// 		},
// 	]

// 	chipsConfig.forEach(config => {
// 		initChipsInput(config)
// 	})

// 	function initChipsInput(config) {
// 		const input = document.getElementById(config.inputId)
// 		const dropdown = document.getElementById(config.dropdownId)
// 		const tagsList = document.getElementById(config.tagsListId)
// 		const wrapper = input.closest('.input-wrapper')

// 		// Показать выпадающий список
// 		function showDropdown() {
// 			dropdown.classList.add('show')
// 		}

// 		// Скрыть выпадающий список
// 		function hideDropdown() {
// 			dropdown.classList.remove('show')
// 		}

// 		// Фильтрация элементов
// 		function filterDropdown() {
// 			const searchTerm = input.value.toLowerCase()
// 			const items = dropdown.querySelectorAll('.tags-dropdown__item')

// 			items.forEach(item => {
// 				const text = item.textContent.toLowerCase()
// 				if (text.includes(searchTerm)) {
// 					item.style.display = 'flex'
// 				} else {
// 					item.style.display = 'none'
// 				}
// 			})
// 		}

// 		// Добавить тег
// 		function addTag(text) {
// 			// Проверка на максимальное количество тегов
// 			if (config.maxTags !== null) {
// 				const currentTagsCount = tagsList.querySelectorAll('li').length
// 				if (currentTagsCount >= config.maxTags) {
// 					alert(`Максимальное количество тегов: ${config.maxTags}`)
// 					return
// 				}
// 			}

// 			// Проверка на дубликаты
// 			const existingTags = Array.from(tagsList.querySelectorAll('li'))
// 			if (existingTags.some(tag => tag.textContent === text)) return

// 			const chip = document.createElement('li')
// 			chip.className = config.chipClass
// 			chip.textContent = text
// 			tagsList.appendChild(chip)
// 		}

// 		// Вернуть опцию в выпадающий список
// 		function returnOptionToDropdown(text) {
// 			const existingItems = Array.from(dropdown.querySelectorAll('.tags-dropdown__item'))
// 			if (existingItems.some(item => item.textContent === text)) return

// 			const option = document.createElement('div')
// 			option.className = 'tags-dropdown__item'
// 			option.textContent = text
// 			dropdown.appendChild(option)
// 		}

// 		// Обработчики событий
// 		input.addEventListener('focus', function () {
// 			showDropdown()
// 			filterDropdown()
// 		})

// 		input.addEventListener('input', filterDropdown)

// 		input.addEventListener('keydown', function (e) {
// 			if (e.key === 'Enter' && this.value.trim()) {
// 				e.preventDefault()
// 				addTag(this.value.trim())
// 				this.value = ''
// 				hideDropdown()
// 			}
// 		})

// 		dropdown.addEventListener('click', function (e) {
// 			const item = e.target.closest('.tags-dropdown__item')
// 			if (item) {
// 				addTag(item.textContent)
// 				item.remove()
// 				input.value = ''
// 				hideDropdown()
// 			}
// 		})

// 		tagsList.addEventListener('click', function (e) {
// 			const chip = e.target.closest('li')
// 			if (chip) {
// 				const rect = chip.getBoundingClientRect()
// 				const isCrossClick = e.clientX > rect.right - 25

// 				if (isCrossClick) {
// 					const tagText = chip.textContent
// 					chip.style.opacity = '0'
// 					chip.style.transform = 'scale(0.8)'

// 					setTimeout(() => {
// 						chip.remove()
// 						returnOptionToDropdown(tagText)
// 					}, 300)
// 				}
// 			}
// 		})

// 		document.addEventListener('click', function (e) {
// 			if (!wrapper.contains(e.target)) {
// 				hideDropdown()
// 			}
// 		})

// 		// Инициализация существующих тегов
// 		function initExistingTags() {
// 			const existingTags = Array.from(tagsList.querySelectorAll('li')).map(tag => tag.textContent)
// 			const dropdownItems = Array.from(dropdown.querySelectorAll('.tags-dropdown__item'))

// 			dropdownItems.forEach(item => {
// 				if (existingTags.includes(item.textContent)) {
// 					item.remove()
// 				}
// 			})
// 		}

// 		initExistingTags()
// 	}
// })


document.addEventListener('DOMContentLoaded', function () {
	// Инициализация счетчиков символов
	initCharCounters()
})

function initCharCounters() {
	// Для диалогового окна (200 символов)
	const fileDescriptionTextarea = document.querySelector('.popUp__fileDescription-textarea')
	if (fileDescriptionTextarea) {
		const counter = document.createElement('div')
		counter.className = 'char-counter'
		counter.textContent = '0/200'
		fileDescriptionTextarea.parentNode.appendChild(counter)

		fileDescriptionTextarea.addEventListener('input', function () {
			const currentLength = this.value.length
			counter.textContent = `${currentLength}/200`
			counter.className = 'char-counter' + (currentLength > 200 ? ' over-limit' : '')

			if (currentLength > 200) {
				this.value = this.value.substring(0, 200)
				counter.textContent = `200/200`
				counter.className = 'char-counter over-limit'
			}
		})
	}

	// Для редакторов tiptap (1000 символов)
	const editors = [
		{ id: 'tiptap-editor-desc', limit: 1000 },
		{ id: 'tiptap-editor-desc2', limit: 1000 },
	]

	editors.forEach(editorConfig => {
		const editor = document.getElementById(editorConfig.id)
		if (!editor) return

		const counter = document.createElement('div')
		counter.className = 'char-counter'
		counter.textContent = '0/' + editorConfig.limit
		editor.parentNode.appendChild(counter)

		// MutationObserver для отслеживания изменений в contenteditable
		const observer = new MutationObserver(() => {
			updateCounter(editor, counter, editorConfig.limit)
		})

		observer.observe(editor, {
			childList: true,
			subtree: true,
			characterData: true,
		})

		// Инициализация при загрузке
		updateCounter(editor, counter, editorConfig.limit)
	})

	// Для скрытых textarea - только ограничение длины без создания счетчика
	const hiddenTextareas = [
		{ id: 'product-description', limit: 1000 },
		{ id: 'product-description2', limit: 1000 },
	]

	hiddenTextareas.forEach(textareaConfig => {
		const textarea = document.getElementById(textareaConfig.id)
		if (!textarea) return

		// Только ограничение длины без создания счетчика
		textarea.addEventListener('input', function () {
			if (this.value.length > textareaConfig.limit) {
				this.value = this.value.substring(0, textareaConfig.limit)
			}
		})
	})

	// Функция обновления счетчика
	function updateCounter(element, counter, limit) {
		let text = element.value || element.innerText
		const currentLength = text.length

		if (currentLength > limit) {
			text = text.substring(0, limit)
			if (element.value) {
				element.value = text
			} else {
				// Для contenteditable
				element.innerText = text

				// Сохраняем позицию курсора
				const selection = window.getSelection()
				const range = document.createRange()
				range.selectNodeContents(element)
				range.collapse(false)
				selection.removeAllRanges()
				selection.addRange(range)
			}
		}

		const displayLength = Math.min(currentLength, limit)
		counter.textContent = `${displayLength}/${limit}`
		counter.className = 'char-counter' + (displayLength >= limit ? ' over-limit' : '')
	}
}