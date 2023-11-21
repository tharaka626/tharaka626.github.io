<script setup>
    import { onMounted ,ref, inject } from 'vue';
    import { useRouter } from 'vue-router';
    const swal = inject('$swal')


    const router = useRouter()

    let form = ref({
        id: ''
    })
    let errors = ref(null)
    let allCustomers = ref([])
    const showModal = ref(false)
    const hideModal = ref(true)
    let listProducts = ref([])

    const props = defineProps({
        id: {
            type:String,
            default:''
        }
    })

    onMounted(async () => {
        getInvoice()
        getAllCustomers()
        getProducts()

    })

    const getInvoice = async () => {
        let response = await axios.get(`/api/invoices/${props.id}`)
        // console.log('form', response.data.invoice);

        form.value = response.data.invoice
    }

    const getAllCustomers = async () => {
        let response = await axios.get('/api/customers')
        // console.log('response', response);
        allCustomers.value = response.data.customers
    }

    const addCart = (item) => {
        const itemCart = {
            product_id : item.id,
            item_code : item.item_code,
            description : item.description,
            unit_price : item.unit_price,
            quantity : item.quantity,
        }

        form.value.invoice_items.push(itemCart)
        closeModal()
    }
    // const removeItem = (i) => {
    //     listCart.value.splice(i, 1)
    // }

    const openModel = () => {
        showModal.value = !showModal.value
    }

    const closeModal = () => {
        showModal.value = !hideModal.value
    }

    const deleteInvoiceItem = (id, i) => {
        swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    form.value.invoice_items.splice(i,1)
                    if (id != undefined) {
                        axios.delete('/api/invoice_items/'+id)
                        swal.fire({
                            title: "Deleted!",
                            text: "Your invoice item has been removed.",
                            icon: "success"
                        });
                    } else {
                        swal.fire({
                            title: "Deleted!",
                            text: "Your invoice item has not been removed.",
                            icon: "error"
                        });
                    }
                }
            });
    }

    const getProducts = async () => {
        let response = await axios.get('/api/products')
        // console.log('response', response);
        listProducts.value = response.data.products
    }

    const subTotal = () => {
        let total = 0

        if (form.value.invoice_items) {
            form.value.invoice_items.map((data)=>{
                total = total + (data.quantity*data.unit_price)
            })
        }

        return total
    }


    const Total = () => {
        if (form.value.invoice_items) {
            return subTotal() - form.value.discount
        }
    }

    const onEdit = (id) => {
        if (form.value.invoice_items.length >= 1) {
            // alert(JSON.stringify(form.value.invoice_items))

            swal.fire({
                title: "Do you want to save the changes?",
                showCancelButton: true,
                confirmButtonText: "Save",
                denyButtonText: `Don't save`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    let subtotal = 0
                    subtotal = subTotal()

                    let total = 0
                    total = Total()

                    const formData = new FormData()
                    formData.append('invoice_item',JSON.stringify(form.value.invoice_items))
                    formData.append('customer_id',form.value.customer_id)
                    formData.append('date',form.value.date)
                    formData.append('due_date',form.value.due_date)
                    formData.append('date',form.value.date)
                    formData.append('number',form.value.number)
                    formData.append('reference',form.value.reference)
                    formData.append('discount',form.value.discount)
                    formData.append('sub_total',subtotal)
                    formData.append('total',total)
                    formData.append('terms_and_conditions',form.value.terms_and_conditions)

                    axios.post(`/api/update_invoice/${form.value.id}`, formData)
                        .then(function(response){
                            form.value.invoice_items = []
                            swal.fire({
                                title: "Saved!",
                                text: "Your invoice has been created.",
                                icon: "success"
                            });
                            router.push('/')
                        })
                        .catch(e => {
                            errors.value = e.response.data.errors
                            // console.log(e.response.data);
                            swal.fire("Changes are not saved", "", "info");
                            throw e
                        });

                } else if (result.isDenied) {
                    swal.fire("Changes are not saved", "", "info");
                }
            });
        } else {
            swal.fire({
                title: "No Items Found",
                text: "'Please enter at least 1 item",
                icon: "error"
            });
        }
    }



</script>

<template>
    <div class="container">
        <div class="invoices">

            <div class="card__header">
                <div>
                    <h2 class="invoice__title">Edit Invoice</h2>
                </div>
                <div>

                </div>
            </div>

            <div class="card__content">
                <div v-if="errors" class="alert alert-danger">
                    <div v-for="(v, k) in errors" :key="k" >
                        <p v-for="error in v" :key="error" class="text-sm">
                        {{ error }}
                        </p>
                    </div>
                </div>
                <div class="card__content--header">
                    <div>
                        <p class="my-1">Customer</p>
                        <select name="" id="" class="input" v-model="form.customer_id">
                            <option disabled>Select a customer</option>
                            <option :value="customer.id"  v-for="customer in allCustomers" :key="customer.id">
                                {{ customer.first_name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <p class="my-1">Date</p>
                        <input id="date" placeholder="dd-mm-yyyy" type="date" class="input" v-model="form.date"> <!---->
                        <p class="my-1">Due Date</p>
                        <input id="due_date" type="date" class="input" v-model="form.due_date">
                    </div>
                    <div>
                        <p class="my-1">Numero</p>
                        <input type="text" class="input" v-model="form.number">
                        <p class="my-1">Reference(Optional)</p>
                        <input type="text" class="input" v-model="form.reference">
                    </div>
                </div>
                <br><br>
                <div class="table">

                    <div class="table--heading2">
                        <p>Item Description</p>
                        <p>Unit Price</p>
                        <p>Qty</p>
                        <p>Total</p>
                        <p></p>
                    </div>

                    <!-- item 1 -->
                    <div class="table--items2" v-for="(itemcart, i) in form.invoice_items" :key="itemcart.id">
                        <p v-if="itemcart.product">
                            #{{ itemcart.product.item_code }} {{ itemcart.product.description }}
                        </p>
                        <p v-else>
                            #{{ itemcart.item_code }} {{ itemcart.description }}
                        </p>
                        <p>
                            <input type="text" class="input" v-model="itemcart.unit_price">
                        </p>
                        <p>
                            <input type="text" class="input" v-model="itemcart.quantity">
                        </p>
                        <p>
                            $ {{ itemcart.unit_price * itemcart.quantity }}
                        </p>
                        <p style="color: red; font-size: 24px;cursor: pointer;" @click="deleteInvoiceItem(itemcart.id, i)">
                            &times;
                        </p>
                    </div>
                    <div style="padding: 10px 30px !important;">
                        <button class="btn btn-sm btn__open--modal" @click="openModel()">
                            Add New Line
                        </button>
                    </div>
                </div>

                <div class="table__footer">
                    <div class="document-footer" >
                        <p>Terms and Conditions</p>
                        <textarea cols="50" rows="7" class="textarea" v-model="form.terms_and_conditions"></textarea>
                    </div>
                    <div>
                        <div class="table__footer--subtotal">
                            <p>Sub Total</p>
                            <span>$ {{ subTotal() }}</span>
                        </div>
                        <div class="table__footer--discount">
                            <p>Discount</p>
                            <input type="text" class="input" v-model="form.discount">
                        </div>
                        <div class="table__footer--total">
                            <p>Grand Total</p>
                            <span>$ {{ Total() }}</span>
                        </div>
                    </div>
                </div>


            </div>
            <div class="card__header" style="margin-top: 40px;">
                <div>
                    <a class="btn btn-secondary" @click="router.push('/invoice/show/'+id)">
                        Back
                    </a>
                </div>
                <div>
                    <a class="btn btn-secondary" @click="onEdit(form.id)">
                        Save
                    </a>
                </div>
            </div>

        </div>
        <!--==================== add modal items ====================-->
        <div class="modal main__modal "  :class="{show:showModal}">
            <div class="modal__content">
                <span class="modal__close btn__close--modal" @click="closeModal()">×</span>
                <h3 class="modal__title">Add Item</h3>
                <hr><br>
                <div class="modal__items">
                    <ul style="list-style: none;">
                        <li v-for="(item, i) in listProducts" :key="item.id" style="display: grid;grid-template-columns: 30px 350px 15px;align-items: center;padding-bottom: 5px;">
                            <p>{{ i+1 }}</p>
                            <a href="#">{{ item.item_code }} {{ item.description }}</a>
                            <button @click="addCart(item)" style="border: 1px solid #e0e0e0;width: 35px;height: 35px;cursor: pointer;">
                                +
                            </button>
                        </li>
                    </ul>
                </div>
                <br><hr>
                <div class="model__footer">
                    <button class="btn btn-light mr-2 btn__close--modal" @click="closeModal()">
                        Cancel
                    </button>
                    <button class="btn btn-light btn__close--modal ">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>
