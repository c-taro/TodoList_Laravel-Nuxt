import Vue from 'vue'
import { ValidationProvider, ValidationObserver, extend } from 'vee-validate'
import { required, numeric, email, max, min } from 'vee-validate/dist/rules' // 使用するバリデーションルールを指定します。

// VeeValidateが用意している各ルールを使用するよう指定
extend('required', required) // 必須項目のバリデーション
extend('numeric', numeric)
extend('email', email)
extend('max', max)
extend('min', min)

// 下記は固定で書く
Vue.component('ValidationProvider', ValidationProvider)
Vue.component('ValidationObserver', ValidationObserver)