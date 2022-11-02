import PropTypes from 'prop-types';
import { PureComponent } from 'react';

import Pagination from 'Component/Pagination';

import Image from '../../component/Image';
import Link from '../../component/Link';

import './IngredientsList.style';
/** @namespace Scandipwa/Component/IngredientsList/Component */
export class IngredientsListComponent extends PureComponent {
    static propTypes = {
        items: PropTypes.arrayOf(PropTypes.arrayOf(PropTypes.string)),
        letters: PropTypes.arrayOf(PropTypes.string),
        categories: PropTypes.arrayOf(PropTypes.string),
        baseUrl: PropTypes.string,
        numberOfPages: PropTypes.number,
        isPagination: PropTypes.bool.isRequired
    };

    static defaultProps = {
        items: [],
        letters: [],
        categories: [],
        numberOfPages: 5,
        baseUrl: ''
    };

    render() {
        const {
            items, letters, categories, isPagination, baseUrl, numberOfPages
        } = this.props;

        if (items.length === 0) {
            return (
                    <h1>no data</h1>
            );
        }

        function data() {
            return (
                <div block="IngredientsList" elem="Grid">
                <ul block="Grid" elem="List">
                { items.map((item, i) => {
                    const keyValue = i + 1;
                    return (

                    <li block="List" elem="Ingredient" key={ keyValue }>
                        <div block="Ingredient" elem="Wrap">
                            <Link href={ item.url } to={ `${baseUrl}ingredients/ingredient/${ item.name}` }>
                                <div block="Wrap" elem="Image">
                                    <Image src={ `${baseUrl}media/ingredients/${ item.img}` } />
                                </div>
                            </Link>
                            <span block="Wrap" elem="Name">{ item.name }</span>
                            <span block="Wrap" elem="LearnMore">Learn More</span>
                        </div>
                    </li>

                    );
                }) }
                </ul>
                </div>
            );
        }

        function ingredientLetters() {
            return (
            <ul block="IngredientsList" elem="Letters">
            <p block="Letters" elem="Label">Filter By Letter &nbsp; &nbsp;</p>
             { letters.map((item, i) => {
                 const keyValue = i + 1;
                 return (

                    <p block="Letters" elem="Letter" key={ keyValue }>
                        <Link
                          block="Letter"
                          elem="Link"
                          to={ `${baseUrl}ingredients/${ item}` }
                          href="random"
                          title={ item }
                        >
                            <span>{ item }</span>
                        </Link>
                    </p>

                 );
             }) }
            </ul>
            );
        }
        function ingredientCategories() {
            return (
                <ul block="IngredientsList" elem="Categories">
                { categories.map((item, i) => {
                    const keyValue = i + 1;
                    return (

                <p block="Categories" elem="Category" key={ keyValue }>
                    <Link href={ item.url } title={ item.id } to={ `${baseUrl}ingredients/${ item.name}` }>
                            <Image src={ item.image } />
                    </Link>
                    <span block="Category" elem="Tooltip">{ item.name }</span>
                    <p block="Category" elem="Label">{ item.name }</p>
                </p>

                    );
                }) }
                </ul>
            );
        }

        function pages() {
            if (isPagination) {
                return (<Pagination totalPages={ numberOfPages } />);
            }

            return (
                    <div />
            );
        }

        return (
            <div block="IngredientsList">
                { ingredientCategories() }
                { ingredientLetters() }
                { data() }
                { pages() }
            </div>
        );
    }
}
export default IngredientsListComponent;
