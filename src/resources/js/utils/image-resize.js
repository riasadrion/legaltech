'use strict';

/**
 * Image resizer utility for client-side image compression before upload
 * Reduces file size and bandwidth, improves upload experience
 */

export const ImageResizer = {
  /**
   * Resize image to max dimensions and return as Blob
   * @param {File} file - Image file to resize
   * @param {number} maxWidth - Maximum width in pixels
   * @param {number} quality - JPEG/WebP quality (0-1), default 0.8
   * @returns {Promise<Blob>}
   */
  async resize(file, maxWidth = 1920, quality = 0.8) {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();

      reader.onload = (e) => {
        const img = new Image();

        img.onload = () => {
          const canvas = document.createElement('canvas');
          let { width, height } = img;

          if (width > maxWidth) {
            height = Math.round((height * maxWidth) / width);
            width = maxWidth;
          }

          canvas.width = width;
          canvas.height = height;

          const ctx = canvas.getContext('2d');
          ctx.drawImage(img, 0, 0, width, height);

          canvas.toBlob(
            (blob) => {
              if (!blob) {
                reject(new Error('Canvas conversion failed'));
                return;
              }
              resolve(blob);
            },
            file.type === 'image/png' ? 'image/png' : 'image/jpeg',
            quality
          );
        };

        img.onerror = () => reject(new Error('Image load failed'));
        img.src = e.target.result;
      };

      reader.onerror = () => reject(new Error('File read failed'));
      reader.readAsDataURL(file);
    });
  },

  /**
   * Resize multiple images
   * @param {FileList|File[]} files - Images to resize
   * @param {number} maxWidth - Maximum width
   * @param {number} quality - JPEG/WebP quality
   * @returns {Promise<Blob[]>}
   */
  async resizeMultiple(files, maxWidth = 1920, quality = 0.8) {
    const fileArray = Array.from(files);
    return Promise.all(
      fileArray.map((file) => this.resize(file, maxWidth, quality))
    );
  },

  /**
   * Get file size in MB
   * @param {Blob} blob
   * @returns {number}
   */
  getFileSizeMb(blob) {
    return (blob.size / 1024 / 1024).toFixed(2);
  },
};
