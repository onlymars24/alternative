<template>
  <ckeditor :editor="editor" v-model="newContent" :config="editorConfig"></ckeditor>
  <el-button style="margin-top: 10px;"  type="primary" :loading="loading" @click="$emit('editStation', this.id, this.title, this.newContent)">{{loading ? 'Загрузка' : 'Сохранить' }}</el-button>
</template>

<script>
import { ClassicEditor } from '@ckeditor/ckeditor5-editor-classic';

import { Essentials } from '@ckeditor/ckeditor5-essentials';
import { Bold, Code, Italic, Strikethrough, Subscript, Superscript, Underline } from '@ckeditor/ckeditor5-basic-styles';
import { Link } from '@ckeditor/ckeditor5-link';
import { Paragraph } from '@ckeditor/ckeditor5-paragraph';
import { SourceEditing } from '@ckeditor/ckeditor5-source-editing';
import { GeneralHtmlSupport } from '@ckeditor/ckeditor5-html-support';
import { Table, TableCellProperties, TableProperties, TableToolbar, TableColumnResize } from '@ckeditor/ckeditor5-table';
import { TodoList, ListProperties, List } from '@ckeditor/ckeditor5-list';
import { Indent, IndentBlock } from '@ckeditor/ckeditor5-indent';
import { Alignment } from '@ckeditor/ckeditor5-alignment';
import { Font } from '@ckeditor/ckeditor5-font';
import { Heading } from '@ckeditor/ckeditor5-heading';
import { ShowBlocks } from '@ckeditor/ckeditor5-show-blocks';
import { SpecialCharacters } from '@ckeditor/ckeditor5-special-characters';
import { SpecialCharactersEssentials } from '@ckeditor/ckeditor5-special-characters';
import { Style } from '@ckeditor/ckeditor5-style';
import { HorizontalLine } from '@ckeditor/ckeditor5-horizontal-line';
import { RemoveFormat } from '@ckeditor/ckeditor5-remove-format';

export default {
  emits: ['editStation'],
  props: ['id', 'title', 'data'],
  data() {
    return {
      editor: ClassicEditor,
      editorConfig: {
          plugins: [
              HorizontalLine,
              Code,
              Strikethrough,
              Subscript,
              Superscript,
              TodoList,
              ListProperties,
              List,
              Essentials,
              Bold,
              Italic,
              Link,
              Paragraph,
              SourceEditing,
              GeneralHtmlSupport,
              Table, 
              TableToolbar,
              Indent,
              IndentBlock,
              TableColumnResize,
              Alignment,
              Font,
              Heading,
              Underline,
              ShowBlocks,
              SpecialCharacters, 
              SpecialCharactersEssentials,
              Style,
              RemoveFormat,
          ],

          toolbar: {
              items: [
                  'undo', 'redo', '|',                    
                  'bold',
                  'italic',
                  'underline', 'strikethrough', 'code', 'subscript', 'superscript', 'horizontalLine', 'removeFormat', '|',
                  'link',
                
                  'insertTable',
                  'bulletedList',
                  'numberedList',
                  'todolist',
                  'outdent',
                  'indent',
                  'alignment',
                  'fontSize',
                  
                  'fontColor',
                  'fontBackgroundColor',
                  'showBlocks',
                  'specialCharacters',
                  'style', 'heading',
                  'sourceEditing',
              ]
          },
          htmlSupport: {
              allow: [
                  {
                      name: /.*/,
                      attributes: true,
                      classes: true,
                      styles: true
                  }
              ]
          },
          table: {
            contentToolbar: [
                'tableColumn', 'tableRow', 'mergeTableCells',
                'tableProperties', 'tableCellProperties'
            ],
          },
          heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
            ]
          },
          style: {
            definitions: [
                {
                    name: 'Article category',
                    element: 'h3',
                    classes: [ 'category' ]
                },
                {
                    name: 'Title',
                    element: 'h2',
                    classes: [ 'document-title' ]
                },
                {
                    name: 'Subtitle',
                    element: 'h3',
                    classes: [ 'document-subtitle' ]
                },
                {
                    name: 'Info box',
                    element: 'p',
                    classes: [ 'info-box' ]
                },
                {
                    name: 'Side quote',
                    element: 'blockquote',
                    classes: [ 'side-quote' ]
                },
                {
                    name: 'Marker',
                    element: 'span',
                    classes: [ 'marker' ]
                },
                {
                    name: 'Spoiler',
                    element: 'span',
                    classes: [ 'spoiler' ]
                },
                {
                    name: 'Code (dark)',
                    element: 'pre',
                    classes: [ 'fancy-code', 'fancy-code-dark' ]
                },
                {
                    name: 'Code (bright)',
                    element: 'pre',
                    classes: [ 'fancy-code', 'fancy-code-bright' ]
                }
            ]
          },
      },
      loading: false,
      newContent: ''
    };
  },
  watch: {

  },
  mounted(){
    this.newContent = JSON.parse(this.data).content
    console.log(this.id)
    console.log(this.title)
    console.log(this.title)
    console.log(this.content)
    console.log(this.newContent)
  }
};
</script>
<style scoped>
    .block{
        max-width: 100%;
    }
</style>