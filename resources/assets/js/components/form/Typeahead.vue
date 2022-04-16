<template>
    <div :class="[isOpen ? 'typeahead typeahead-open' : 'typeahead']">
        <div class="typeahead-inner">
            <div :class="['typeahead-selected', 'form-control']" :tabindex="tabindex"
                ref="toggle" @click="toggle" @keydown="onKey"
                :title="selected.text">
                <span>{{selected.text | trim(trim)}}</span>
                <span class="typeahead-toggler">
                  <i class="fa fa-angle-up" v-if="isOpen"></i>
                  <i class="fa fa-angle-down" v-else></i>
                </span>
            </div>
            <transition name="fade" mode="out-in">
            <div class="typeahead-dropdown" v-if="isOpen">
                <div class="typeahead-input_wrap">
                    <input autocomplete="off" type="text" spellcheck="false"
                        autocorrect="off" autocapitalize="off"
                        class="typeahead-input" ref="search"
                        placeholder="Search..."
                        @blur="onBlur" @keydown.esc="onEsc"
                        @input="onSearch" @keydown.down="onDownKey"
                        @keydown.up="onUpKey" @keydown.enter="onEnterKey"
                        >
                    <transition name="fade">
                    <i class="fa fa-spinner fa-pulse typeahead-spinner"
                        v-if="isLoading"></i>
                        </transition>
                </div>
                <ul class="typeahead-list" v-if="results.length">
                    <li class="typeahead-item" v-for="(result, index) in results" >
                        <a class="ac-result" :class="[selectIndex === index ? 'typeahead-active':'']"
                          @mousedown.prevent="select(result)"
                          @mouseover.prevent="onMouse(index)">{{result.text}}</a>
                    </li>
                </ul>
                <div class="typeahead-empty" v-else-if="!isLoading">
                    <small>No Results found!</small>
                </div>
            </div>
            </transition>
        </div>
    </div>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import { get } from '../../lib/api'
    export default {
        props: {
            tabindex: {
                type: Number,
                default: 0
            },
            trim: {
                type: Number,
                default: 45
            },
            url: {
                type: String,
                required: true
            },
            initial: {
                type: Object
            },
            default: {
                type: Object,
                default() {
                    return { id: null, text: 'Type or click to select' }
                }
            },
            params: {
                type: Object,
                default() {
                    return {}
                }
            }
        },
        data() {
            return {
                isLoading: false,
                selectIndex: -1,
                isOpen: false,
                search: '',
                results: []
            }
        },
        computed: {
            selected() {
                return  this.initial
                    ? this.initial
                    : this.default
            }
        },
        methods:{
            onEnterKey () {
                const option = this.results[this.selectIndex]

                if(option) {
                    this.select(option)
                }
            },
            onDownKey() {
                if(this.results.length -1 > this.selectIndex) {
                    this.selectIndex++
                }
            },
            onUpKey() {
                if(this.selectIndex > 0) {
                    this.selectIndex--
                }
            },
            select(result) {
                this.$emit('input', {
                    target: {
                        value: result
                    }
                })
                this.onEsc()
            },
            onMouse(index) {
                this.selectIndex = index
            },
            onBlur () {
              this.close()
            },
            onEsc () {
              this.$refs.search.blur()
            },
            close () {
              this.results = []
              this.isOpen = false
              this.search = ''
              this.selectIndex = -1
            },
            onSearch(event) {
                const q = event.target.value
                this.selectIndex = 0
                this.fetchData(q)
            },
            toggle () {
              if (this.isOpen) {
                this.isOpen = false
              } else {
                this.open()
              }
            },
            onKey (e) {
              const keyCode = e.keyCode || e.which
              if (!e.shiftKey && keyCode !== 9 && !this.isOpen) {
                this.open()
              }
            },
            open () {
              this.isOpen = true
              this.$nextTick(() => {
                this.$refs.search.focus()
              })

              this.fetchData('')
            },
            fetchData(q) {
                this.isLoading = true
                const params = {
                    q: q,
                    ...this.params
                }
                get(this.url, params)
                    .then((res) => {
                        Vue.set(this.$data, 'results', res.data.results)
                        this.isLoading = false
                    })
            },
        }
    }
</script>
