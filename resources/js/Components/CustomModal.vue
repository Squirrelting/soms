<template>
    <div class="modal-overlay" @click.self="close">
      <div class="modal" :class="modalSize">
        <div class="modal-header">
          <h3>{{ title }}</h3>
        </div>
        <div class="modal-body">
          <slot></slot>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      title: {
        type: String,
        default: "Confirmation"
      },
      width: {
        type: String,
        default: "sm" // Can be "sm", "md", "lg" for width control
      }
    },
    computed: {
      modalSize() {
        return {
          'w-1/3': this.width === 'sm',
          'w-1/2': this.width === 'md',
          'w-2/3': this.width === 'lg',
        };
      }
    },
    methods: {
      close() {
        this.$emit('close');
      }
    }
  };
  </script>
  
  <style scoped>
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .modal {
    background-color: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  }
  
  .modal-header h3 {
    margin: 0;
  }
  
  .modal-body {
    margin: 15px 0;
  }
  </style>
  