document.addEventListener('DOMContentLoaded', function () {
	initCharCounters()
})

function initCharCounters() {
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

		const observer = new MutationObserver(() => {
			updateCounter(editor, counter, editorConfig.limit)
		})

		observer.observe(editor, {
			childList: true,
			subtree: true,
			characterData: true,
		})

		updateCounter(editor, counter, editorConfig.limit)
	})

	const hiddenTextareas = [
		{ id: 'product-description', limit: 1000 },
		{ id: 'product-description2', limit: 1000 },
	]

	hiddenTextareas.forEach(textareaConfig => {
		const textarea = document.getElementById(textareaConfig.id)
		if (!textarea) return

		textarea.addEventListener('input', function () {
			if (this.value.length > textareaConfig.limit) {
				this.value = this.value.substring(0, textareaConfig.limit)
			}
		})
	})

	function updateCounter(element, counter, limit) {
		let text = element.value || element.innerText
		const currentLength = text.length

		if (currentLength > limit) {
			text = text.substring(0, limit)
			if (element.value) {
				element.value = text
			} else {
				element.innerText = text

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