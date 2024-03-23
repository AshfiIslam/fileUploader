<template>
    <div class="upload-box">
        <p>Upload your files</p>
        <form>
            <input
                class="file-input"
                type="file"
                name="file"
                ref="fileInput"
                hidden
                @change="uploadFile"
            />
            <div class="icon" @click="$refs.fileInput.click()">
                <!-- <img :src="`/public/image/icon/abc.png`" alt="" /> -->
                <img src="/image/icon/abc.png" alt="">
            </div>

            <p @click="$refs.fileInput.click()">Browse file to upload</p>
        </form>
        <section class="loading-area" v-if="showProgress">
            <li class="row" v-for="(file, index) in files">
                <i class="fas fa-file-alt"></i>
                <!-- <i class="fa-solid fa-file-lines"></i> -->
                <!-- <i class="fa-regular fa-file-lines"></i> -->
                <div class="content">
                    <div class="details">
                        <span class="name">{{ file.name }}</span>
                        <span class="percent">{{ file.loading }}</span>
                    </div>
                    <div class="loading-bar">
                        <div
                            class="loading"
                            :style="{ width: file.loading + '%' }"
                        ></div>
                    </div>
                </div>
            </li>
        </section>

        <section class="upload-area">
            <li class="row" v-for="(file, index) in uploadFiles">
                <div class="content upload">
                    <i class="fa fa-file-text" aria-hidden="true"></i>
                    <div class="details">
                        <span class="name"> {{ file.name }}</span>
                        <span class="size"> {{ file.size }}</span>
                    </div>
                </div>
                <i class="fa fa-check" aria-hidden="true"></i>
            </li>
        </section>

        <section>
            <button type="submit" class="btn btn-primary"@click="saveFiles">Save</button>
        </section>
    </div>
</template>

<script>
export default (await import("vue")).defineComponent({
    data() {
        return {
            files: [],
            uploadFiles: [],
            showProgress: false,
        };
    },
    mounted() {
        localStorage.setItem("uniqueID", Math.random().toString(36).slice(2));
    },
    methods: {
        saveFiles() {
            var uniqueId = localStorage.getItem("uniqueID");
            axios
                .post("save-file", {
                    files: this.uploadFiles,
                    uniqueId: uniqueId,
                })
                .then((res) => {
                    this.file = [];
                    this.uploadFiles = [];
                    console.log("save");
                })
                .catch((error) => {
                    console.error("Error saving file:", error);
                });
        },
        uploadFile(event) {
            const file = event.target.files[0];
            console.log(file);
            if (!file) return;
            const fileName =
                file.name.length >= 12
                    ? file.name.substring(0, 13) +
                      "... ." +
                      file.name.split(".")[1]
                    : file.name;
            const formData = new FormData();
            formData.append("file", file);
            this.files.push({ name: fileName, loading: 0 });
            console.log("ok", this.files);
            this.showProgress = true;

            axios
                .post("upload-file", formData, {
                    onUploadProgress: ({ loaded, total }) => {
                        this.files[this.files.length - 1].loading = Math.floor(
                            (loaded / total) * 100
                        );
                        if (loaded == total) {
                            const fileSize =
                                total < 1024
                                    ? total + "KB"
                                    : (loaded / (1024 * 1024)).toFixed(2) +
                                      "MB";
                            this.uploadFiles.push({
                                name: fileName,
                                size: fileSize,
                            });
                            this.files = [];
                            this.showProgress = false;
                        }
                    },
                })
                .catch(console.error);
            //             const chunkSize = 10 * 1024 * 1024;
            // const file =  event.target.files[0] ;
            // const fileSize = file.size;
            // let start = 0;

            // while (start < fileSize) {
            //     const chunk = file.slice(start, start + chunkSize);
            //     const formData = new FormData();
            //     formData.append('file', chunk);
            //     formData.append('start', start);
            //     formData.append('end', start + chunkSize);

            //     axios.post('upload-file', formData, {
            //         onUploadProgress: ({ loaded, total }) => {
            //             // Update progress if needed
            //         }
            //     });

            //     start += chunkSize;
            // }
        },
    },
});
</script>
<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}
body {
    display: flex;
    padding: 10px;
    background: #1aaa4c;
}
.upload-box > p {
    text-align: center;
    font-weight: 500;
    margin-top: 10px;
}
img {
    width: 120px;
    height: 80px;
    
    
}
.icon {
    width: 90px;
    height: 90px;
    background: white;
}
.upload-box {
    width: 100%;
    background: #fff;
    border-radius: 5px;
    padding: 15px;
    box-shadow: 7px 7px 12px rgba(0, 0, 0, 0.05);
}
.upload-box form {
    height: 170px;
    display: flex;
    cursor: pointer;
    margin-top: 20px;
    margin-bottom: 10px;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    border-radius: 5px;
    border: 2px dashed #29ba2a;
}
form :where(i, p) {
    color: #29ba2a;
}
form i {
    font-size: 50px;
}
form p {
    margin-top: 15px;
    font-size: 16px;
}
section .row {
    margin-bottom: 10px;
    background: #d0ffd3;
    list-style: none;
    padding: 10px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
section .row {
    color: #1aaa4c;
    font-size: 30px;
}
section .details span {
    font-size: 12px;
}
.loading-area .row .content {
    width: 100%;
    margin-left: 15px;
}
.loading-area .details {
    display: flex;
    align-items: center;
    margin-bottom: 7px;
    justify-content: space-between;
}
.loading-area .content .loading-bar {
    height: 6px;
    width: 100%;
    margin-bottom: 4px;
    background: #fff;
    border-radius: 30px;
}
.content .loading-bar .loading {
    height: 100%;
    width: 20%;
    background: #1aaa4c;
    border-radius: inherit;
}
.upload-area {
    max-height: 232px;
    overflow-y: scroll;
}
.upload-area.onprogress {
    max-height: 150px;
}
.upload-area::-webkit-scrollbar {
    width: 0px;
}
.upload-area .row .content {
    display: flex;
    align-items: center;
}
.upload-area .row .details {
    display: flex;
    margin-left: 15px;
    flex-direction: column;
}
.upload-area .row .details .size {
    color: #404040;
    font-size: 11px;
}
.upload-area i.fa-check {
    font-size: 11px;
}
</style>
