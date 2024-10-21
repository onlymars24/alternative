<template>
    <div class="common-layout" v-loading.fullscreen.lock="false">
        <!-- <div class="container"> -->
            <Header/>
            <el-container>
                <el-main style="min-height: 500px;">
                    <ckeditor :editor="editor" v-model="editorData" :config="editorConfig"></ckeditor>
                    <el-button style="margin-top: 10px;" @click="editUpcomingTrips" type="primary" :loading="loading">{{loading ? 'Загрузка' : 'Сохранить' }}</el-button>
                </el-main>

            </el-container>
        <!-- </div> -->
    </div>
</template>

<script>

// import axios from 'axios';
import axiosAdmin from '../../axiosAdmin'
import Header from '../../components/admin/Header.vue'

// import CKEditor from '@ckeditor/ckeditor5-vue';
import { ClassicEditor } from '@ckeditor/ckeditor5-editor-classic';

import { Essentials } from '@ckeditor/ckeditor5-essentials';
import { Bold, Code, Italic, Strikethrough, Subscript, Superscript, Underline } from '@ckeditor/ckeditor5-basic-styles';
import { Link } from '@ckeditor/ckeditor5-link';
import { Paragraph } from '@ckeditor/ckeditor5-paragraph';
import { SourceEditing } from '@ckeditor/ckeditor5-source-editing';
import { GeneralHtmlSupport } from '@ckeditor/ckeditor5-html-support';
import { Table, TableCellProperties, TableProperties, TableToolbar, TableColumnResize  } from '@ckeditor/ckeditor5-table';
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
//   components: { Header, ckeditor: CKEditor.component },
  components: { Header },
  data(){
    return {
        race: [],
        loading: false,
        editor: ClassicEditor,
        editorData: '',
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
        }
    }
  },
  async mounted(){
    const promise = axiosAdmin
    .get('/page/upcoming/trips')
    .then(response => {
        console.log(response)
        this.editorData = response.data.pageUpcomingTrips
    })
    .catch(error => {
        console.log(error)
    })
    await promise
  },
  methods: {
    async editUpcomingTrips(){
        this.loading = true
        const promise = axiosAdmin
        .post('/page/upcoming/trips', {content: this.editorData})
        .then(response => {
            console.log(response)
        })
        .catch(error => {
            console.log(error)
        })
        await promise
        this.loading = false
    }
  }
}
</script>
<style>
  /* > * + * {
    margin-top: 0.75em;
  } */
  .ck ul,  .ck ol{
    margin: 0px;
    padding: 20px;
    /* list-style-type: inherit; */
  }
  .ck li{
    margin: 0px;
    padding: 0px;
    list-style-type: inherit;
  }
  .ck.ck-content h3.category {
        font-family: 'Bebas Neue';
        font-size: 20px;
        font-weight: bold;
        color: #555;
        letter-spacing: 10px;
        margin: 0;
        padding: 0;
    }

    .ck.ck-content h2.document-title {
        font-family: 'Bebas Neue';
        font-size: 50px;
        font-weight: bold;
        margin: 0;
        padding: 0;
        border: 0;
    }

    .ck.ck-content h3.document-subtitle {
        font-family: 'Bebas Neue';
        font-size: 20px;
        color: #555;
        margin: 0 0 1em;
        font-weight: normal;
        padding: 0;
    }

    .ck.ck-content p.info-box {
        --background-size: 30px;
        --background-color: #e91e63;
        padding: 1.2em 2em;
        border: 1px solid var(--background-color);
        background: linear-gradient(135deg, var(--background-color) 0%, var(--background-color) var(--background-size), transparent var(--background-size)), linear-gradient(135deg, transparent calc(100% - var(--background-size)), var(--background-color) calc(100% - var(--background-size)), var(--background-color));
        border-radius: 10px;
        margin: 1.5em 2em;
        box-shadow: 5px 5px 0 #ffe6ef;
    }

    .ck.ck-content blockquote.side-quote {
        font-family: 'Bebas Neue';
        font-style: normal;
        float: right;
        width: 35%;
        position: relative;
        border: 0;
        overflow: visible;
        z-index: 1;
        margin-left: 1em;
    }

    .ck.ck-content blockquote.side-quote::before {
        content: "“";
        position: absolute;
        top: -37px;
        left: -10px;
        display: block;
        font-size: 200px;
        color: #e7e7e7;
        z-index: -1;
        line-height: 1;
    }

    .ck.ck-content blockquote.side-quote p {
        font-size: 2em;
        line-height: 1;
    }

    .ck.ck-content blockquote.side-quote p:last-child:not(:first-child) {
        font-size: 1.3em;
        text-align: right;
        color: #555;
    }

    .ck.ck-content span.marker {
        background: yellow;
    }

    .ck.ck-content span.spoiler {
        background: #000;
        color: #000;
    }

    .ck.ck-content span.spoiler:hover {
        background: #000;
        color: #fff;
    }

    .ck.ck-content pre.fancy-code {
        border: 0;
        margin-left: 2em;
        margin-right: 2em;
        border-radius: 10px;
    }

    .ck.ck-content pre.fancy-code::before {
        content: "";
        display: block;
        height: 13px;
        background: url(data:image/svg+xml;base64,PHN2ZyBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1NCAxMyI+CiAgPGNpcmNsZSBjeD0iNi41IiBjeT0iNi41IiByPSI2LjUiIGZpbGw9IiNGMzZCNUMiLz4KICA8Y2lyY2xlIGN4PSIyNi41IiBjeT0iNi41IiByPSI2LjUiIGZpbGw9IiNGOUJFNEQiLz4KICA8Y2lyY2xlIGN4PSI0Ny41IiBjeT0iNi41IiByPSI2LjUiIGZpbGw9IiM1NkM0NTMiLz4KPC9zdmc+Cg==);
        margin-bottom: 8px;
        background-repeat: no-repeat;
    }

    .ck.ck-content pre.fancy-code-dark {
        background: #272822;
        color: #fff;
        box-shadow: 5px 5px 0 #0000001f;
    }

    .ck.ck-content pre.fancy-code-bright {
        background: #dddfe0;
        color: #000;
        box-shadow: 5px 5px 0 #b3b3b3;
    }
</style>