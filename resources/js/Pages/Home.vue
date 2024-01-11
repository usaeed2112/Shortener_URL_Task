<template>
    <div class="card m-5">
        <div class="card-header">
            <h2>Shortten your urls!</h2>
        </div>
        <div class="card-body">
            <form
                role="form"
                @submit.prevent="shortUrl"
                @keydown="form.onKeydown($event)"
            >
                <div class="card-body">
                    <div class="row">
                        <div class="form-group">
                            <label for="product_name"
                                >Url <span class="required">*</span></label
                            >
                            <input
                                id="url"
                                v-model="form.url"
                                type="text"
                                class="form-control"
                                :class="{
                                    'is-invalid': form.errors.has('url'),
                                }"
                                name="url"
                                :placeholder="'Enter Url'"
                            />
                            <has-error :form="form" field="url" />
                            <p class="text-danger" v-if="short_url">
                                {{ short_url }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <v-button :loading="form.busy" class="btn btn-primary">
                        save
                    </v-button>
                    <button
                        type="reset"
                        class="btn btn-secondary float-right"
                        @click="form.reset()"
                    >
                        reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import toastr from "toastr";
import Form from "vform";
import Swal from "sweetalert2";
import VButton from "./Button.vue";
import { HasError } from "vform/src/components/bootstrap4";

export default {
    name: "Home",
    components: {
        HasError,
        VButton,
    },
    data() {
        return {
            short_url: "",
            form: new Form({
                url: "",
            }),
        };
    },
    methods: {
        async shortUrl() {
            await this.form
                .post(window.location.origin + "/api/short/url")
                .then((res) => {
                    this.short_url = res.data.url;
                    toastr.success("succes");
                    Swal.fire({
                        title: "Success",
                        titleText: "URL Created Successfully",
                    });
                })
                .catch(() => {
                    toastr.error("error");
                });
        },
    },
};
</script>
