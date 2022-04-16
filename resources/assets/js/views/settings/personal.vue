<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">Personal Settings</span>
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
                        <h3>Basic</h3>
                    </div>
                    <div class="col col-8">
                        <div class="row">
                            <div class="col col-8">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control" v-model="form.name">
                                    <error-text :error="error.name"></error-text>
                                </div>
                                <div class="form-group">
                                    <label>Job Title</label>
                                    <input type="text" class="form-control" v-model="form.title">
                                    <error-text :error="error.title"></error-text>
                                </div>
                                <div class="form-group">
                                    <label>Mobile Number</label>
                                    <input type="text" class="form-control" v-model="form.mobile_number">
                                    <error-text :error="error.mobile_number"></error-text>
                                </div>
                                <div class="form-group">
                                    <label>Telephone</label>
                                    <input type="text" class="form-control" v-model="form.telephone">
                                    <error-text :error="error.telephone"></error-text>
                                </div>
                                <div class="form-group">
                                    <label>Extension</label>
                                    <input type="text" class="form-control" v-model="form.extension">
                                    <error-text :error="error.extension"></error-text>
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
                            <div class="col col-8">
                                <div class="form-group">
                                    <label>
                                        Email Signature
                                        <a @click.stop="generate">Generate</a>
                                    </label>
                                    <textarea class="form-control" v-model="form.email_signature"></textarea>
                                    <error-text :error="error.email_signature"></error-text>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col col-4">
                        <h3>Security</h3>
                        <br>
                        <small>To change your password, specify and confirm a new password.</small>
                    </div>
                    <div class="col col-8">
                        <div class="row">
                            <div class="col col-8">
                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" class="form-control" v-model="form.current_password">
                                    <error-text :error="error.current_password"></error-text>
                                </div>
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" class="form-control" v-model="form.new_password">
                                    <error-text :error="error.new_password"></error-text>
                                </div>
                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <input type="password" class="form-control" v-model="form.new_password_confirmation">
                                    <error-text :error="error.new_password_confirmation"></error-text>
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
            'create': `/api/personal_settings`
        }

        return (urls[to.meta.mode] || urls['create'])
    }

    export default {
        components: { ErrorText, Typeahead, ImageUploadPreview, Spinner },
        mixins: [ form ],
        data () {
            return {
                store: '/api/personal_settings',
                method: 'POST',
                message: 'You have successfully updated personal settings!'
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
            generate() {
                this.form.email_signature = `Best Regards\n\n${this.form.name}\n${this.form.title}, ${this.form.company}\nEmail: ${this.form.email}\nTel: ${this.form.telephone} Ext: ${this.form.extension}\nMob: ${this.form.mobile_number}`
            },
            setData(res) {
                Vue.set(this.$data, 'form', res.data.form)
                this.$title.set(`Personal Settings`)
                this.$bar.finish()
                this.show = true
            },
             save() {
                this.submit((data) => {
                    this.success()
                    window.scroll(0, 1)
                    this.$bar.start()
                    this.endProcessing()
                    this.$bar.finish()
                    window.apex.user.name = this.form.name
                })
            }
        }
    }
</script>
