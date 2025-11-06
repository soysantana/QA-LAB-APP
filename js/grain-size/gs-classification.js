function clasificarSuelo(gravel, sand, fines, Cu, Cc) {
    let code = "";
    let description = "";

    if (gravel > sand && fines < 5 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand < 15) {
        code = "GW";
        description = "Well graded gravel";
    } else if (gravel > sand && fines < 5 && Cu >= 4 && Cc >= 0.5 && Cc <= 3 && sand >= 15) {
        code = "GW";
        description = "Well graded gravel with sand";
    } else if (gravel > sand && fines < 5 && (Cu < 4 || Cc < 1 || Cc > 3) && sand < 15) {
        code = "GP";
        description = "Poorly graded gravel";
    } else if (gravel > sand && fines < 5 && (Cu < 4 || Cc < 1 || Cc > 3) && sand >= 15) {
        code = "GP";
        description = "Poorly graded gravel with sand";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand < 15) {
        code = "GW-GM";
        description = "Well graded gravel with silt";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand >= 15) {
        code = "GW-GM";
        description = "Well graded gravel with silt and sand";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand < 15) {
        code = "GW-GC";
        description = "Well graded gravel with clay";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand >= 15) {
        code = "GW-GC";
        description = "Well graded gravel with clay and sand";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu < 4 && Cc < 1 && Cc > 3 && sand < 15) {
        code = "GP-GM";
        description = "Poorly graded gravel with silt";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && (Cu < 4 || Cc < 1 || Cc > 3) && sand >= 15) {
        code = "GP-GM";
        description = "Poorly graded gravel with silt and sand";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu < 4 && Cc < 1 && Cc > 3 && sand < 15) {
        code = "GP-GC";
        description = "Poorly graded gravel with clay";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu < 4 && Cc < 1 && Cc > 3 && sand >= 15) {
        code = "GP-GC";
        description = "Poorly graded gravel with clay and sand";
    } else if (gravel > sand && fines >= 5 && fines > 12 && sand < 15) {
        code = "GM";
        description = "Silty gravel";
    } else if (gravel > sand && fines >= 5 && fines > 12 && sand >= 15) {
        code = "GM";
        description = "Silty gravel with sand";
    } else if (gravel > sand && fines >= 5 && fines > 12 && sand < 15) {
        code = "GC";
        description = "Clayey gravel";
    } else if (gravel > sand && fines >= 5 && fines > 12 && sand >= 15) {
        code = "GC";
        description = "Clayey gravel with sand";
    } else if (gravel > sand && fines >= 5 && fines > 12 && sand < 15) {
        code = "GC-GM";
        description = "Silty clayey gravel";
    } else if (gravel > sand && fines >= 5 && fines > 12 && sand >= 15) {
        code = "GC-GM";
        description = "Silty clayey gravel with sand";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && (Cu < 4 || Cc < 1 || Cc > 3) && sand >= 15) {
        code = "GP-GM";
        description = "Poorly graded gravel with silt and sand";
    } else if (sand > gravel && fines < 5 && Cu >= 6 && Cc >= 0.5 && Cc <= 3 && gravel < 15) {
        code = "SW";
        description = "Well graded sand";
    } else if (sand > gravel && fines < 5 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel >= 15) {
        code = "SW";
        description = "Well graded sand with gravel";
    } else if (sand > gravel && fines < 5 && (Cu < 6 || Cc < 1 || Cc > 3) && gravel < 15) {
        code = "SP";
        description = "Poorly graded sand";
    } else if (sand > gravel && fines < 5 && (Cu < 6 || Cc < 1 || Cc > 3) && gravel >= 15) {
        code = "SP";
        description = "Poorly graded sand with gravel";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel < 15) {
        code = "SW-SM";
        description = "Well graded sand with silt";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel >= 15) {
        code = "SW-SM";
        description = "Well graded sand with silt and gravel";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel < 15) {
        code = "SW-SC";
        description = "Well graded sand with clay";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel >= 15) {
        code = "SW-SC";
        description = "Well graded sand with clay and gravel";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && (Cu < 6 || Cc < 1 || Cc > 3) && gravel < 15) {
        code = "SP-SM";
        description = "Poorly graded sand with silt";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu < 6 && Cc < 1 && Cc > 3 && gravel >= 15) {
        code = "SP-SM";
        description = "Poorly graded sand with silt and sand";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu > 6 && Cc >= 1 && Cc <= 3.4 && gravel >= 15) {
        code = "SP-SM";
        description = "Poorly graded sand with silt and gravel";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu < 6 && Cc < 1 && Cc > 3 && gravel < 15) {
        code = "SP-SC";
        description = "Poorly graded sand with clay";
    } else if (sand > gravel && fines >= 5 && fines <= 12 && Cu < 6 && Cc < 1 && Cc > 3 && gravel >= 15) {
        code = "SP-SC";
        description = "Poorly graded sand with clay and gravel";
    } else if (sand > gravel && fines >= 5 && fines > 12 && gravel < 15) {
        code = "SM";
        description = "Silty sand";
    } else if (sand > gravel && fines >= 5 && fines > 12 && gravel >= 15) {
        code = "SM";
        description = "Silty sand with gravel";
    } else if (sand > gravel && fines >= 5 && fines > 12 && gravel < 15) {
        code = "SC";
        description = "Clayey sand";
    } else if (sand > gravel && fines >= 5 && fines > 12 && gravel >= 15) {
        code = "SC";
        description = "Clayey sand with gravel";
    } else if (sand > gravel && fines >= 5 && fines > 12 && gravel < 15) {
        code = "SC-GM-Silty clayey sand";
        description = "Silty clayey sand";
    } else if (sand > gravel && fines >= 5 && fines > 12 && gravel >= 15) {
        code = "SC-GM";
        description = "Silty clayey sand with gravel";
    } else if (gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand >= 15) {
        code = "GW";
        description = "Well graded gravel with fines and sand";
    } else {
        code = "NA";
        description = "No se pudo clasificar el suelo.";
    }

    return { code, description };
}

function clasificarSueloExtra(WtRetExtendidaArray) {
    const extraInfo =
        (WtRetExtendidaArray[0] !== 0 && WtRetExtendidaArray[4] !== 0 && WtRetExtendidaArray[9] !== 0)
            ? "with Boulders and Cobbles"
            : (WtRetExtendidaArray[4] !== 0)
                ? "Boulders"
                : (WtRetExtendidaArray[4] !== 0 && WtRetExtendidaArray[9] !== 0)
                    ? "with Cobbles"
                    : "";

    return extraInfo;
}

export { clasificarSuelo, clasificarSueloExtra };