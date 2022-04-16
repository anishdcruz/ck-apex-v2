<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">{{model.name}}</span>
                <div>
                    <router-link :to="`/users`" class="btn" title="Go Back">
                        <i class="fa fa-arrow-left"></i>
                    </router-link>
                    <router-link :to="`/users/${model.id}/edit`"
                        class="btn" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </router-link>
                    <a @click.stop="deleteModel" class="btn btn-danger">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col col-4">
                        <div class="bg-grey">
                            <div class="row">
                                <div class="col col-2">
                                    <i class="fa fa-user"></i>
                                </div>
                                <div class="col col-10">
                                    <p>{{model.name}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-2">
                                    <i class="fa fa-star"></i>
                                </div>
                                <div class="col col-10">
                                    <p>{{model.title}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-2">
                                    <i class="fa fa-envelope"></i>
                                </div>
                                <div class="col col-10">
                                    <p>{{model.email}}</p>
                                </div>
                            </div>
                            <div class="row" v-if="model.telephone">
                                <div class="col col-2">
                                    <i class="fa fa-phone"></i>
                                </div>
                                <div class="col col-10">
                                    <p>{{model.telephone}}</p>
                                </div>
                            </div>
                            <div class="row" v-if="model.extension">
                                <div class="col col-2">
                                    <i class="fa fa-phone-square"></i>
                                </div>
                                <div class="col col-10">
                                    <p>{{model.extension}}</p>
                                </div>
                            </div>
                            <div class="row" v-if='model.mobile_number'>
                                <div class="col col-2">
                                    <i class="fa fa-mobile"></i>
                                </div>
                                <div class="col col-10">
                                    <p>{{model.mobile_number}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-2">
                                    <i class="fa fa-lock"></i>
                                </div>
                                <div class="col col-10">
                                    <p>
                                        <span class="status status-accepted" v-if="model.is_admin">Yes</span>
                                        <span class="status status-draft" v-else>No</span>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-2">
                                    <i class="fa fa-check"></i>
                                </div>
                                <div class="col col-10">
                                    <p>
                                        <span class="status status-paid" v-if="model.is_active">Yes</span>
                                        <span class="status status-draft" v-else>No</span>
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <strong>Email Signature</strong><br><br>
                            <pre>{{model.email_signature}}</pre>
                        </div>
                    </div>
                    <div class="col col-8"></div>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import { get, byMethod } from '../../lib/api'
    export default {
        data() {
            return {
                show: false,
                model: {
                    currency: {}
                }
            }
        },
        beforeRouteEnter(to, from, next) {
            get(`/api/users/${to.params.id}`)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            this.show = false
            get(`/api/users/${to.params.id}`)
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            deleteModel() {
                const r = confirm("Are you sure!")
                if(r != true) { return }
                this.$bar.start()
                byMethod('delete', `/api/users/${this.model.id}`)
                    .then(({data}) => {
                        if(data.deleted) {
                            this.$router.push('/users')
                            this.$message.success(`You have successfully deleted user!`)
                        }
                        this.$bar.finish()
                    })
                    .catch((err) => {})
            },
            setData(res) {
                Vue.set(this.$data, 'model', res.data.data)
                this.$title.set(`Users - ${this.model.name}`)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>
