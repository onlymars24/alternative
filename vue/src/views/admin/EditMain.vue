<template>
    <div class="common-layout" v-loading.fullscreen.lock="false">
        <!-- <div class="container"> -->
            <Header/>
            <el-container>
                <el-main style="min-height: 500px;">
                    <ckeditor :editor="editor" v-model="editorData" :config="editorConfig"></ckeditor>
                    <el-button style="margin-top: 10px;" @click="editMain" type="primary" :loading="loading">{{loading ? 'Загрузка' : 'Сохранить' }}</el-button>
                </el-main>

            </el-container>
        <!-- </div> -->
    </div>
</template>

<script>

// import axios from 'axios';
import axiosClient from '../../axios';
import Header from '../../components/admin/Header.vue'

// import CKEditor from '@ckeditor/ckeditor5-vue';
import { ClassicEditor } from '@ckeditor/ckeditor5-editor-classic';

import { Essentials } from '@ckeditor/ckeditor5-essentials';
import { Bold, Italic } from '@ckeditor/ckeditor5-basic-styles';
import { Link } from '@ckeditor/ckeditor5-link';
import { Paragraph } from '@ckeditor/ckeditor5-paragraph';
import { SourceEditing } from '@ckeditor/ckeditor5-source-editing';
import { GeneralHtmlSupport } from '@ckeditor/ckeditor5-html-support';



// import { SourceEditing } from '@ckeditor/ckeditor5-source-editing';


// import Code from '@tiptap/extension-code'
// import Document from '@tiptap/extension-document'
// import Paragraph from '@tiptap/extension-paragraph'
// import Text from '@tiptap/extension-text'
// import Typography from '@tiptap/extension-typography'

// import { ColorHighlighter } from './ColorHighlighter.ts'
// import { SmilieReplacer } from './SmilieReplacer.ts'

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
                Essentials,
                Bold,
                Italic,
                Link,
                Paragraph,
                SourceEditing,
                GeneralHtmlSupport
            ],

            toolbar: {
                items: [
                    'bold',
                    'italic',
                    'link',
                    'undo',
                    'redo',
                    'sourceEditing'
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
            }
        }
    }
  },
  async mounted(){
    const promise = axiosClient
    .get('/page/main')
    .then(response => {
        console.log(response)
        this.editorData = response.data.pageMain
    })
    .catch(error => {
        console.log(error)
    })
    await promise
  },
  methods: {
    async editMain(){
        this.loading = true
        const promise = axiosClient
        .post('/page/main', {content: this.editorData})
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

  .tiptap{
        outline: none;
        border: 1px solid transparent;
        padding: 0;
        border: 1px solid black;
        border-radius: 7px;
        height: 300px;
    }
  .tiptap ul,
  .tiptap ol {
    padding: 0 1rem;
    list-style-type: auto !important;
  }
  .tiptap__buttons{
    margin-bottom: 10px;
  }
  .tiptap__buttons button{
    border: 1px solid black;
    border-radius: 5px;
    margin-right: 5px;
    padding: 3px;
  }
  .tiptap__buttons .is-active{
    background-color: black;
    color: white;
  }
  

  .tiptap h1,
  .tiptap h2,
  .tiptap h3,
  .tiptap h4,
  .tiptap h5,
  .tiptap h6 {
    line-height: 1.1;
  }

  .tiptap code {
    background-color: rgba(#616161, 0.1);
    color: #616161;
  }

  .tiptap pre {
    background: #0D0D0D;
    color: #FFF;
    font-family: 'JetBrainsMono', monospace;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;


  }
    .tiptap pre code {
      color: inherit;
      padding: 0;
      background: none;
      font-size: 0.8rem;
    }
  .tiptap img {
    max-width: 100%;
    height: auto;
  }

  .tiptap blockquote {
    padding-left: 1rem;
    border-left: 2px solid rgba(#0D0D0D, 0.1);
  }

  .tiptap hr {
    border: none;
    border-top: 2px solid rgba(#0D0D0D, 0.1);
    margin: 2rem 0;
  }
</style>