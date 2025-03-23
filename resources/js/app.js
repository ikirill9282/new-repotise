import './bootstrap';
import 'quill/dist/quill.snow.css';
import Quill from 'quill';

window.Quill = Quill;

document.addEventListener('DOMContentLoaded', function () {
  const editors = document.querySelectorAll('.quill-editor');
  
  editors.forEach(editor => {
      const quill = new Quill(editor, {
          theme: 'snow',
          modules: {
              toolbar: [
                  ['bold', 'italic', 'underline'],
                  ['link', 'image'],
                  [{ list: 'ordered'}, { list: 'bullet' }],
                  ['clean']                                         // Remove formatting button
              ]
          }
      });

      quill.on('text-change', function() {
          // Обновляем скрытое поле с содержимым редактора
          const content = quill.root.innerHTML;
          editor.value = content; // Обновляем значение поля
      });
  });
});