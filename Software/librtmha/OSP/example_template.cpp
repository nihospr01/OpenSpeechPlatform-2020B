//
// Created by Dhiman Sengupta on 10/3/18.
//

#ifndef OSP_WDRC_H
#define OSP_WDRC_H

#include <memory>
#include <atomic>
#include <OSP/GarbageCollector/GarbageCollector.hpp>

/**
 * @brief A template for library modules
 */

class libModule{

public:
    /**
     * @brief libModule constructor
     */
    explicit libModule(...){
        ...
    }
    /**
     * @brief libModule destructor
     */
    ~libModule(){
        ...
    }
    /**
     * @brief Setting libModule parameters
     */
    void set_param(...){

        /* Create a new libModule_param_t structure for the new
         * parameters, which is sharable
         */
        std::shared_ptr<libModule_param_t> data_next =
                std::make_shared<libModule_param_t> ();
        /* Updated data_next with the new parameter values, this
         * is where the one time calculations take place.
         */
        ...
        /* Add the new structure to the garbage collector*/
        gc.add(data_next);
        /* Set new structure as the current global structure
         * atomically
         */
        std::atomic_store(&currentParam, data_next);
        /* Run the garbage collection*/
        gc.release();

    }

    /**
     * @brief Getting libModule parameters
     */
    void get_param(...){
        /* Load the current global param structure
         * atomically
         */
        std::shared_ptr<libModule_param_t> data_current =
                std::atomic_load(&currentParam);
        /* Return all of the parameters by reference from
         * the structure that was loaded above
         */
        ...
    }

    /**
     * @brief Real-time processing inside libModule
     */
    void process(...){
        /* Load the current global param structure
         * atomically
         */
        std::shared_ptr<libModule_param_t> data_current =
                std::atomic_load(&currentParam);
        /* This is where the real-time code will go*/
    }

private:

    struct libModule_param_t {
        /* Set of Parameters that are consumed
         * by the real-time processing
         */
    };

    std::shared_ptr<libModule_param_t> currentParam;
    GarbageCollector gc;

};

#endif //OSP_WDRC_H
