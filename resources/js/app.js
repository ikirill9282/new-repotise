import './bootstrap';
import 'quill/dist/quill.snow.css';

// import { autocomplete } from '@algolia/autocomplete-js'
// import {
//   meilisearchAutocompleteClient,
//   getMeilisearchResults,
// } from '@meilisearch/autocomplete-client'

// import '@algolia/autocomplete-theme-classic'

import AirDatepicker from 'air-datepicker';
import 'air-datepicker/air-datepicker.css';

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
                  [{ 'align': ['', 'center', 'right', 'justify'] }],
                  ['clean']
              ]
          },
          placeholder: editor.getAttribute('data-placeholder') ?? '',
      });

      quill.on('text-change', function() {
          const content = quill.root.innerHTML;
          editor.value = content;
      });
  });


  // const token = '0EHXuNA1FV';

  // const searchClient = meilisearchAutocompleteClient({
  //   url: 'http://localhost:7700', // Host
  //   apiKey: token,  // API key
  // })
        

  // autocomplete({
  //   container: '#autocomplete',
  //   openOnFocus: true,
  //   placeholder: 'Search for games',
  //   getSources({ query }) {
  //     if (!query) return [];
  //     return [
  //       {
  //         sourceId: 'articles',
  //         getItems() {
  //           return getMeilisearchResults({
  //             searchClient: searchClient,
  //             queries: [
  //               {
  //                 indexName: 'articles',
  //                 query,
  //                 attributesToHighlight: [],
  //               },
  //             ],
  //             transformResponse(data) {
  //               console.log(data);
                
  //             }
  //           })
  //         },
  //         templates: {
  //           item({ item, components, html }) {
  //             return html`<div>
  //               <div>${item.title}</div>
  //             </div>`
  //           },
  //         },
  //       },
  //     ]
  //   },
  // })
});
