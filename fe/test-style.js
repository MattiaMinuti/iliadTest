// Test file for frontend auto-fix
const testObject = {
  name: 'test',
  value: 123,
  items: [1, 2, 3, 4, 5],
};

function testFunction(param1, param2) {
  if (param1 === param2) {
    console.log('equal');
  } else {
    console.log('different');
  }
}

const arrowFunction = (x, y) => x + y;

export { testObject, testFunction, arrowFunction };
