module.exports = {
  // mode: 'jit',
  purge: [
    './**/*.php',
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {
      colors: {
        'clearly-white': '#fff',
        'clearly-black': '#000',
        white: '#E8E8E8', // #FFF8E5
        black: '#222831',
        muted: '#6b7280',
        primary: '#222831',
        red: {
          '50': '#fdf7f7',
          '100': '#fcefef',
          '200': '#f7d7d7',
          '300': '#f3bebe',
          '400': '#e98e8e',
          '500': '#e05d5d',
          '600': '#ca5454',
          '700': '#a84646',
          '800': '#863838',
          '900': '#6e2e2e'
        },
        green: {
            '50': '#f2fafa',
            '100': '#e6f6f5',
            '200': '#bfe8e7',
            '300': '#99d9d8',
            '400': '#4dbdba',
            '500': '#00a19d',
            '600': '#00918d',
            '700': '#007976',
            '800': '#00615e',
            '900': '#004f4d'
        },
        yellow: {
            '50': '#fffbf6',
            '100': '#fff7ec',
            '200': '#ffecd0',
            '300': '#ffe1b4',
            '400': '#ffca7c',
            '500': '#ffb344',
            '600': '#e6a13d',
            '700': '#bf8633',
            '800': '#996b29',
            '900': '#7d5821'
        }
      }
    },
  },
  variants: {
    extend: {
      placeholderColor: ['hover', 'focus'],
    },
  },
  plugins: [],
}
