export function parseTreeNode(treeNode) {
    return {
        left: {
            g50: [
                parseFloat(treeNode['g50_250']),
                parseFloat(treeNode['g50_500']),
                parseFloat(treeNode['g50_1000']), 
                parseFloat(treeNode['g50_2000']),
                parseFloat(treeNode['g50_4000']),
                parseFloat(treeNode['g50_6000']), 
            ],
            g80: [
                parseFloat(treeNode['g80_250']),
                parseFloat(treeNode['g80_500']),
                parseFloat(treeNode['g80_1000']), 
                parseFloat(treeNode['g80_2000']),
                parseFloat(treeNode['g80_4000']),
                parseFloat(treeNode['g80_6000']), 
            ],
            knee_low: [
                parseFloat(treeNode['kneelow_250']),
                parseFloat(treeNode['kneelow_500']),
                parseFloat(treeNode['kneelow_1000']), 
                parseFloat(treeNode['kneelow_2000']),
                parseFloat(treeNode['kneelow_4000']),
                parseFloat(treeNode['kneelow_6000']), 
            ],
            mpo_band: [
                parseFloat(treeNode['mpoband_250']),
                parseFloat(treeNode['mpoband_500']),
                parseFloat(treeNode['mpoband_1000']), 
                parseFloat(treeNode['mpoband_2000']),
                parseFloat(treeNode['mpoband_4000']),
                parseFloat(treeNode['mpoband_6000']), 
            ],
        },
        right: {
            g50: [
                parseFloat(treeNode['g50_250']),
                parseFloat(treeNode['g50_500']),
                parseFloat(treeNode['g50_1000']), 
                parseFloat(treeNode['g50_2000']),
                parseFloat(treeNode['g50_4000']),
                parseFloat(treeNode['g50_6000']), 
            ],
            g80: [
                parseFloat(treeNode['g80_250']),
                parseFloat(treeNode['g80_500']),
                parseFloat(treeNode['g80_1000']), 
                parseFloat(treeNode['g80_2000']),
                parseFloat(treeNode['g80_4000']),
                parseFloat(treeNode['g80_6000']), 
            ],
            knee_low: [
                parseFloat(treeNode['kneelow_250']),
                parseFloat(treeNode['kneelow_500']),
                parseFloat(treeNode['kneelow_1000']), 
                parseFloat(treeNode['kneelow_2000']),
                parseFloat(treeNode['kneelow_4000']),
                parseFloat(treeNode['kneelow_6000']), 
            ],
            mpo_band: [
                parseFloat(treeNode['mpoband_250']),
                parseFloat(treeNode['mpoband_500']),
                parseFloat(treeNode['mpoband_1000']), 
                parseFloat(treeNode['mpoband_2000']),
                parseFloat(treeNode['mpoband_4000']),
                parseFloat(treeNode['mpoband_6000']), 
            ],
        },
    }
};

export function parseTreeNodeSingleEar(treeNode, leftEarOnly) {
    if (leftEarOnly) {
        return {
            left: {
                g50: [
                    parseFloat(treeNode['g50_250']),
                    parseFloat(treeNode['g50_500']),
                    parseFloat(treeNode['g50_1000']), 
                    parseFloat(treeNode['g50_2000']),
                    parseFloat(treeNode['g50_4000']),
                    parseFloat(treeNode['g50_6000']), 
                ],
                g80: [
                    parseFloat(treeNode['g80_250']),
                    parseFloat(treeNode['g80_500']),
                    parseFloat(treeNode['g80_1000']), 
                    parseFloat(treeNode['g80_2000']),
                    parseFloat(treeNode['g80_4000']),
                    parseFloat(treeNode['g80_6000']), 
                ],
                knee_low: [
                    parseFloat(treeNode['kneelow_250']),
                    parseFloat(treeNode['kneelow_500']),
                    parseFloat(treeNode['kneelow_1000']), 
                    parseFloat(treeNode['kneelow_2000']),
                    parseFloat(treeNode['kneelow_4000']),
                    parseFloat(treeNode['kneelow_6000']), 
                ],
                mpo_band: [
                    parseFloat(treeNode['mpoband_250']),
                    parseFloat(treeNode['mpoband_500']),
                    parseFloat(treeNode['mpoband_1000']), 
                    parseFloat(treeNode['mpoband_2000']),
                    parseFloat(treeNode['mpoband_4000']),
                    parseFloat(treeNode['mpoband_6000']), 
                ],
            },
            right: {},
        };
    } else {
        return {
            left: {},
            right: {
                g50: [
                    parseFloat(treeNode['g50_250']),
                    parseFloat(treeNode['g50_500']),
                    parseFloat(treeNode['g50_1000']), 
                    parseFloat(treeNode['g50_2000']),
                    parseFloat(treeNode['g50_4000']),
                    parseFloat(treeNode['g50_6000']), 
                ],
                g80: [
                    parseFloat(treeNode['g80_250']),
                    parseFloat(treeNode['g80_500']),
                    parseFloat(treeNode['g80_1000']), 
                    parseFloat(treeNode['g80_2000']),
                    parseFloat(treeNode['g80_4000']),
                    parseFloat(treeNode['g80_6000']), 
                ],
                knee_low: [
                    parseFloat(treeNode['kneelow_250']),
                    parseFloat(treeNode['kneelow_500']),
                    parseFloat(treeNode['kneelow_1000']), 
                    parseFloat(treeNode['kneelow_2000']),
                    parseFloat(treeNode['kneelow_4000']),
                    parseFloat(treeNode['kneelow_6000']), 
                ],
                mpo_band: [
                    parseFloat(treeNode['mpoband_250']),
                    parseFloat(treeNode['mpoband_500']),
                    parseFloat(treeNode['mpoband_1000']), 
                    parseFloat(treeNode['mpoband_2000']),
                    parseFloat(treeNode['mpoband_4000']),
                    parseFloat(treeNode['mpoband_6000']), 
                ],
            },
        };
    }
};


export const NUM_LEVEL = 3;
export const NUM_TREE = 5;