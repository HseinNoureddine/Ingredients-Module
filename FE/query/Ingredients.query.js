import { Field } from 'Util/Query';

/** @namespace Scandipwa/Query/Ingredients/Query */
export class IngredientsQuery {
    getIngredientsFields() {
        return [
            'id',
            'name',
            'img',
            'url',
            'description',
            'letter',
            'categoryID'
        ];
    }

    getIngredientFields() {
        const products = new Field('products').addFieldList([
            'id',
            'name',
            'img',
            'url'
        ]);
        const metaData = new Field('metaData').addFieldList([
            'metaTitle',
            'metaKeyWords',
            'metaDescription'
        ]);

        return [
            'id',
            'name',
            'img',
            'url',
            'description',
            'letter',
            'categoryID',
            metaData,
            products
        ];
    }

    getCategoryFields() {
        return [
            'id',
            'name',
            'image',
            'url'
        ];
    }

    getIngredientField() {
        return new Field('ingredients').addFieldList(this.getIngredientsFields());
    }

    getCategoryField() {
        return new Field('categories').addFieldList(this.getCategoryFields());
    }

    getIngredientCollection(pageNumber, pageSize) {
        return new Field('ingredientCollection')
            .addArgument('pageSize', 'Int!', pageSize)
            .addArgument('pageNumber', 'Int!', pageNumber)
            .addField('numberOfPages')
            .addField('baseUrl')
            .addField(this.getIngredientField());
    }

    getIngredient(id, pageSize, pageNumber) {
        return new Field('ingredient')
            .addArgument('id', 'String!', id)
            .addArgument('pageSize', 'Int!', pageSize)
            .addArgument('pageNumber', 'Int!', pageNumber)
            .addField('numberOfProductPages')
            .addFieldList(this.getIngredientFields());
    }

    getCategoryCollection() {
        return new Field('categoryCollection')
            .addField(this.getCategoryField());
    }

    getBaseUrl(pageSize = 0, pageNumber = 0) {
        return new Field('ingredientCollection')
            .addArgument('pageSize', 'Int!', pageSize)
            .addArgument('pageNumber', 'Int!', pageNumber)
            .addField('baseUrl');
    }
}

export default new IngredientsQuery();
