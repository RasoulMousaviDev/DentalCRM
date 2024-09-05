import { defineStore } from "pinia";

export const usePhotosStore = defineStore("photos", {
    state: () => ({
        items: [],
        pagiantor: {},
        fetching: true,
    }),
    actions: {
        async index({ page = 1, rows = 10, ...query }) {
            this.items = [];
            this.fetching = true;

            const { statusText, data } = await this.axios.get("/photos", {
                params: { page, rows, ...query },
            });
            this.fetching = false;

            if (statusText === "OK") {
                this.items = data.items;
                this.items.map((item) => {
                    this.axios
                        .get(`/photos/${item.id}`, { responseType: "blob" })
                        .then(
                            (res) =>
                                (item.image = URL.createObjectURL(res.data))
                        );
                });
                this.pagiantor = data.pagiantor;
            }
        },
        async store(form) {
            const formData = new FormData();
            Object.entries(form).forEach(([key, value]) => {
                formData.append(key, value);
            });
            const res = await this.axios.post("/photos", formData);

            if (res.statusText === "OK") {
                res.data.image = URL.createObjectURL(form.photo);
                this.items.unshift(res.data);
            }

            return res;
        },
        async update(id, form) {
            delete form.id;

            const formData = new FormData();

            Object.entries(form).forEach(([key, value]) => {
                if (!(key == "image" && typeof value == "string"))
                    formData.append(key, value);
            });

            const res = await this.axios.post(`/photos/${id}`, formData);

            if (res.statusText === "OK") {
                const index = this.items.findIndex((item) => item.id === id);

                res.data.image =
                    typeof form.image === "object"
                        ? URL.createObjectURL(form.image)
                        : form.image;

                this.items[index] = res.data;
            }

            return res;
        },

        async destroy(id) {
            const res = await this.axios.delete(`/photos/${id}`);

            if (res.statusText === "OK") {
                const index = this.items.findIndex(
                    (photo) => photo.id === id
                );
                this.items.splice(index, 1);
            }

            return res;
        },
    },
});
