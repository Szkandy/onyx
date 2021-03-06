#!/bin/sh
FULL_PATH="."
SCREEN_CSS="${FULL_PATH}/share/css/default/screen.css";

echo "------------------------------------------------";
echo "COMPILING ${SCREEN_CSS}";
echo "------------------------------------------------";
echo "/* compiled by utils/compile_css.sh */" > ${SCREEN_CSS}
cat ${FULL_PATH}/share/css/default/src/font.css >> ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/normalize.css >> ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/common.css >> ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/layout.css >> ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/structure.css >> ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/node.css >> ${SCREEN_CSS};
cat ${FULL_PATH}/share/css/default/src/component.css >> ${SCREEN_CSS};
#cat ${FULL_PATH}/share/css/default/src/component_ecommerce.css >> ${SCREEN_CSS};
