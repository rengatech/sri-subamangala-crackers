import { createStore } from 'vuex';

const CART_KEY = 'cartItems';

function saveCart(state) {
  localStorage.setItem(CART_KEY, JSON.stringify(state.cartItems));
}

// Exact 2-decimal rounding (no whole-rupee rounding)
export const round2 = (n) => Math.round((Number(n) + Number.EPSILON) * 100) / 100;
export const formatPrice = (n) => round2(n).toFixed(2);

// Same rule as OrderController::getItemTotal():
// per-unit discount is rounded to 2 decimals, only for items whose category has a discount.
function unitDiscount(item, discountPercent) {
  if (item.has_discount === false) return 0;
  return round2((Number(item.price) * discountPercent) / 100);
}

export const store = createStore({
  state: {
    cartItems: [],
  },

  mutations: {
    initializeCartItems(state) {
      const saved = localStorage.getItem(CART_KEY);
      if (saved) {
        try {
          state.cartItems = JSON.parse(saved);
        } catch {
          state.cartItems = [];
        }
      }
    },

    updateItemInCart(state, payload) {
      let item = payload;
      if (state.cartItems.length > 0) {
        let bool = state.cartItems.some(i => i.id === item.id);
        if (bool) {
          let itemIndex = state.cartItems.findIndex(el => el.id === item.id);
          if (item.quantity == 0 || item.quantity <= 0) {
            state.cartItems.splice(itemIndex, 1);
          } else {
            state.cartItems[itemIndex]["quantity"] = item.quantity;
          }
        } else {
          state.cartItems.push(item);
        }
      } else {
        state.cartItems.push(item);
      }
      saveCart(state);
    },

    addToCart(state, payload) {
      let item = { ...payload, quantity: 1 };

      if (state.cartItems.length > 0) {
        let bool = state.cartItems.some(i => i.id === item.id);
        if (bool) {
          let itemIndex = state.cartItems.findIndex(el => el.id === item.id);
          state.cartItems[itemIndex]["quantity"] += 1;
        } else {
          state.cartItems.push(item);
        }
      } else {
        state.cartItems.push(item);
      }
      saveCart(state);
    },

    removeItemFromCart(state, payload) {
      if (state.cartItems.length > 0) {
        let bool = state.cartItems.some(i => i.id === payload.id);
        if (bool) {
          let index = state.cartItems.findIndex(el => el.id === payload.id);
          if (state.cartItems[index]["quantity"] !== 0) {
            state.cartItems[index]["quantity"] -= 1;
          }
          if (state.cartItems[index]["quantity"] === 0) {
            state.cartItems.splice(index, 1);
          }
        }
      }
      saveCart(state);
    },

    deleteFromCart(state, payload) {
      const index = state.cartItems.findIndex(i => i.id === payload.id);
      if (index !== -1) {
        state.cartItems.splice(index, 1);
      }
      saveCart(state);
    },

    clearCart(state) {
      state.cartItems = [];
      saveCart(state);
    },
  },

  getters: {
    totalPrice(state) {
      let total = 0;
      for (const item of state.cartItems) {
        total += item.quantity * Number(item.price);
      }
      return round2(total);
    },

    // Total discount amount for the whole cart. Usage: store.getters.discountTotal(global_discount)
    discountTotal: (state) => (discountPercent) => {
      let total = 0;
      for (const item of state.cartItems) {
        total += unitDiscount(item, discountPercent) * item.quantity;
      }
      return round2(total);
    },

    // Final payable amount. Usage: store.getters.netTotal(global_discount)
    netTotal: (state, getters) => (discountPercent) => {
      return round2(getters.totalPrice - getters.discountTotal(discountPercent));
    },

    totalItems(state) {
      return state.cartItems.length;
    },

    priceByItem: (state) => (itemId) => {
      const item = state.cartItems.find((item) => item.id === itemId);
      if (item) {
        return round2(item.quantity * Number(item.price));
      }
      return 0;
    },

    countByItem: (state) => (itemId) => {
      const item = state.cartItems.find((item) => item.id === itemId);
      if (item) {
        return item.quantity;
      }
      return 0;
    },

    getOrderItems(state) {
      return state.cartItems;
    },
  },

  actions: {
    addToCart: (context, payload) => {
      context.commit("addToCart", payload);
    },
    removeItem: (context, payload) => {
      context.commit("removeItem", payload);
    },
    initializeCart(context) {
      context.commit('initializeCartItems');
    },
    clearItem(context) {
      context.commit('clearCart');
    },
  },
});

store.dispatch('initializeCart');