<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">Settings</span>
                <div>
                    <spinner v-if="isProcessing"></spinner>
                    <div class="btn-group" v-else>
                        <button :disabled="isProcessing" @click="save" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col col-4">
                        <h3>Account</h3>
                    </div>
                    <div class="col col-8">
                        <div class="row">
                            <div class="col col-8">
                                <div class="form-group">
                                    <label>App Title</label>
                                    <input type="text" class="form-control" v-model="form.app_title">
                                    <error-text :error="error.app_title"></error-text>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-8">
                                <div class="form-group">
                                    <label>Currency</label>
                                    <typeahead :initial="form.currency"
                                        :url="currencyURL"
                                        @input="onCurrencyUpdate"
                                    >
                                    </typeahead>
                                    <error-text :error="error.currency_id"></error-text>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-4">
                                <div class="form-group">
                                    <label>Upload Logo</label>
                                    <image-upload-preview width="200" height="150"
                                        :preview="form.uploaded_logo"
                                        @ready="onReadyFile('uploaded_logo_file', $event)"
                                        @remove="onRemoveFile('uploaded_logo')" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col col-4">
                        <h3>Company</h3>
                    </div>
                    <div class="col col-8">
                        <div class="row">
                            <div class="col col-8">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control" v-model="form.company_name">
                                    <error-text :error="error.company_name"></error-text>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-8">
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea class="form-control" v-model="form.company_address"></textarea>
                                    <error-text :error="error.company_address"></error-text>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-8">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" class="form-control" v-model="form.company_email">
                                    <error-text :error="error.company_email"></error-text>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-8">
                                <div class="form-group">
                                    <label>Website</label>
                                    <input type="text" class="form-control" v-model="form.company_website">
                                    <error-text :error="error.company_website"></error-text>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-6">
                                <div class="form-group">
                                    <label>Telephone</label>
                                    <input type="text" class="form-control" v-model="form.company_telephone">
                                    <error-text :error="error.company_telephone"></error-text>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col col-4">
                        <h3>Email</h3>
                    </div>
                    <div class="col col-8">
                        <div class="row">
                            <div class="col col-6">
                                <div class="form-group">
                                    <label>Sent From Name</label>
                                    <input type="text" class="form-control" v-model="form.sent_from_name">
                                    <error-text :error="error.sent_from_name"></error-text>
                                </div>
                            </div>
                            <div class="col col-6">
                                <div class="form-group">
                                    <label>Sent From Email</label>
                                    <input type="text" class="form-control" v-model="form.sent_from_email" />
                                    <error-text :error="error.sent_from_email"></error-text>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-8">
                                <div class="form-group">
                                    <label>
                                        Global BCC Email
                                        <small>Optional</small>
                                    </label>
                                    <input type="text" class="form-control" v-model="form.global_bcc_email">
                                    <error-text :error="error.global_bcc_email"></error-text>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-12">
                                <div class="form-group">
                                    <label>Footer Line 1</label>
                                    <input type="text" class="form-control" v-model="form.footer_line_1">
                                    <error-text :error="error.footer_line_1"></error-text>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-12">
                                <div class="form-group">
                                    <label>Footer Line 2</label>
                                    <input type="text" class="form-control" v-model="form.footer_line_2">
                                    <error-text :error="error.footer_line_2"></error-text>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-12">
                                <div class="form-group">
                                    <label>Footer Line 3</label>
                                    <input type="text" class="form-control" v-model="form.footer_line_3">
                                    <error-text :error="error.footer_line_3"></error-text>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col col-4">
                        <h3>Document</h3>
                    </div>
                    <div class="col col-8">
                        <div class="row">
                            <div class="col col-4">
                                <div class="form-group">
                                    <label>Upload Header</label>
                                    <image-upload-preview width="600" height="150"
                                        :preview="form.header"
                                        @ready="onReadyFile('header_file', $event)" @remove="onRemoveFile('header')" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-4">
                                <div class="form-group">
                                    <label>Upload Footer</label>
                                    <image-upload-preview width="600" height="150"
                                        :preview="form.footer"
                                        @ready="onReadyFile('footer_file', $event)" @remove="onRemoveFile('footer')" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <spinner v-if="isProcessing"></spinner>
                <div class="btn-group" v-else>
                    <button :disabled="isProcessing" @click="save" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import ImageUploadPreview from '../../components/form/ImageUploadPreview.vue'
    import ErrorText from '../../components/form/ErrorText.vue'
    import Typeahead from '../../components/form/Typeahead.vue'
    import Spinner from '../../components/loading/Spinner.vue'
    import { get, byMethod } from '../../lib/api'
    import { form } from '../../lib/mixins'

    function initializeUrl (to) {
        let urls = {
            'create': `/api/settings`
        }

        return (urls[to.meta.mode] || urls['create'])
    }

    export default {
        components: { ErrorText, Typeahead, ImageUploadPreview, Spinner },
        mixins: [ form ],
        data () {
            return {
                store: '/api/settings',
                method: 'POST',
                message: 'You have successfully updated settings!',
                currencyURL: '/api/search/currencies'
            }
        },
        beforeRouteEnter(to, from, next) {

            get(initializeUrl(to))
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            this.show = false

            get(initializeUrl(to))
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            setData(res) {
                Vue.set(this.$data, 'form', res.data.form)
                this.$title.set(`Settings`)
                this.$bar.finish()
                this.show = true
            },
            onCurrencyUpdate(e) {
                const currency = e.target.value

                Vue.set(this.form, 'currency_id', currency.id)
                Vue.set(this.form, 'currency', currency)
            },
            onReadyFile(name, e) {
                const file = e.target.value

                Vue.set(this.form, name, file)
            },
            onRemoveFile(file) {
                Vue.set(this.form, file, null)
            },
             save() {
                this.submitMultipartForm(this.form, (data) => {
                    this.success()
                    window.scroll(0, 1)
                    this.$bar.start()
                    this.endProcessing()
                    this.$bar.finish()
                })
            }
        }
    }
</script>
