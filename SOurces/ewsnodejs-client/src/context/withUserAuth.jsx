import React from 'react';
import { UserConsumer } from 'context/userContext';

export default function withUserAuth(Component) {
    return function userAuthComponent(props) {
        return (
            <UserConsumer>
                {context => <Component {...props} context={context}/>}
            </UserConsumer>
        );
    }
}